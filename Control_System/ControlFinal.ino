#include <util/atomic.h>

#define ENCA 3      //Pin de Encoder A motor
#define ENCB 2      //Pin de Encoder B motor
#define PWM_PIN 6   //Pin de PWM del motor
#define DIR_PIN1 4  //Pin de dirección del motor
#define DIR_PIN2 5  //Pin de dirección del motor

#define PULSE_PIN 19   // Pin de interrupción para el pulso
#define VALUE_PIN 18   // Pin para leer el valor (0 o 1)

//Varoables para controlador
volatile long posi = 0;
volatile unsigned long prevT = 0;
volatile long posPrev = 0;
float setpoint_angle = 0.0; // Ángulo deseado inicial
float previous_angle_error = 0.0;
float velocity_setpoint = 0.0;
float previous_velocity_error = 0.0;
float velocity_integral = 0.0;
const float pulses_per_degree = 408.0 / 360.0;
const float pulses_per_rev = 408;
const float max_velocity_rpm = 130.0;

float Kp = 0.01;
float Ki = 30;
float Kd = 0.01;

volatile bool new_setpoint = false; // Flag para indicar un nuevo setpoint
volatile unsigned long timer_start = 0;

void setup() {
  Serial.begin(9600);
  pinMode(ENCA, INPUT);
  pinMode(ENCB, INPUT);
  pinMode(DIR_PIN1, OUTPUT);
  pinMode(DIR_PIN2, OUTPUT);
  pinMode(PWM_PIN, OUTPUT);
  pinMode(PULSE_PIN, INPUT_PULLUP);
  pinMode(VALUE_PIN, INPUT);

  attachInterrupt(digitalPinToInterrupt(ENCA), readEncoder, RISING);
  attachInterrupt(digitalPinToInterrupt(PULSE_PIN), handlePulse, FALLING);

  prevT = micros();
  setMotorDirection(true);

  // Variables para timer de interrupciones
  noInterrupts();
  TCCR1A = 0; 
  TCCR1B = (1 << WGM12) | (1 << CS12) | (1 << CS10);
  TIMSK1 = (1 << OCIE1A);
  OCR1A = 7812;
  interrupts();
  delay(5000);
}

void loop() {
  unsigned long currT = micros();
  float deltaT = (currT - prevT) / 1000000.0;
  if (deltaT <= 0.0) return;

  long pos;
  ATOMIC_BLOCK(ATOMIC_RESTORESTATE) {
    pos = posi;
  }

  //Calcular error angular
  float angle_deg = pos / pulses_per_degree;
  float angle_error = setpoint_angle - angle_deg;
  //Definir set point de velocidad
  velocity_setpoint = (angle_error - previous_angle_error) / deltaT;
  previous_angle_error = angle_error;

  const float alpha = 0.02;
  static float smoothed_velocity_setpoint = 0;
  smoothed_velocity_setpoint = alpha * velocity_setpoint + (1 - alpha) * smoothed_velocity_setpoint;

  if (smoothed_velocity_setpoint > max_velocity_rpm) {
    smoothed_velocity_setpoint = max_velocity_rpm;
  } else if (smoothed_velocity_setpoint < -max_velocity_rpm) {
    smoothed_velocity_setpoint = -max_velocity_rpm;
  }

  //Calcular velocidad real y error de velocidad
  float velocity = ((float)(pos - posPrev)) / deltaT / pulses_per_rev * 60.0;
  float velocity_error = smoothed_velocity_setpoint - velocity;
  //Elementos derivados e integrales de la velocidad
  float velocity_derivative = (velocity_error - previous_velocity_error) / deltaT;
  velocity_integral += velocity_error * deltaT;
  velocity_integral = constrain(velocity_integral, -255 / Ki, 255 / Ki);
  //Calcular salida PWM
  float output = Kp * velocity_error + Ki * velocity_integral + Kd * velocity_derivative;
  output = constrain(output, -255, 255);
  if (angle_error > 0) {
    setMotorDirection(true);
  } else {
    setMotorDirection(false);
  }

  analogWrite(PWM_PIN, abs(output));
  //Análisis de datos
  Serial.print("Data,");
  Serial.print(angle_error);
  Serial.print(',');
  Serial.print(smoothed_velocity_setpoint);
  Serial.print(',');
  Serial.print(output);
  Serial.print(',');
  Serial.print(velocity);
  Serial.print(',');
  Serial.print(angle_deg);
  Serial.print(',');
  Serial.println(currT / 1000000.0, 6);

  previous_velocity_error = velocity_error;
  posPrev = pos;
  prevT = currT;
  //Criterio para dejar de suministrar energía al motor
  if (abs(angle_error) <= 2.0) {
    analogWrite(PWM_PIN, 0);
    velocity_integral = 0;
  } else {
    analogWrite(PWM_PIN, abs(output));
  }
  delay(10);
}

void readEncoder() { //Lectura de pulsos del encoder
  int b = digitalRead(ENCB);
  if (b > 0) {
    posi++;
  } else {
    posi--;
  }
}

void setMotorDirection(bool direction) { //Indicar dirección de motor
  if (direction) {
    digitalWrite(DIR_PIN1, HIGH);
    digitalWrite(DIR_PIN2, LOW);
  } else {
    digitalWrite(DIR_PIN1, LOW);
    digitalWrite(DIR_PIN2, HIGH);
  }
}

void handlePulse() { //Mapeo de señal de Raspberry Pi a grados
  int value = digitalRead(VALUE_PIN);
  if (value == HIGH) {
    setpoint_angle = 90.0;
  } else {
    setpoint_angle = -90.0;
  }
  new_setpoint = true;
  timer_start = millis();
}

// Interrupción del Timer1
ISR(TIMER1_COMPA_vect) {
  if (new_setpoint && millis() - timer_start >= 1000) {
    setpoint_angle = 0.0;
    new_setpoint = false;
  }
}
