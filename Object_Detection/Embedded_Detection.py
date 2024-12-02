# Import libraries
import cv2
import logging
from ultralytics import YOLO
import time
import RPi.GPIO as GPIO

# GPIO for the switch and output pins
SWITCH_PIN = 17  # Switch
PIN_PULSE = 18  # Reading pulse
PIN_RESULT = 23  # Detection result

GPIO.setmode(GPIO.BCM)  # BCM numbering
GPIO.setup(SWITCH_PIN, GPIO.IN, pull_up_down=GPIO.PUD_UP)  # Configure the switch pin as input with pull-up
GPIO.setup(PIN_PULSE, GPIO.OUT)
GPIO.setup(PIN_RESULT, GPIO.OUT)

# Initialize pins to low state
GPIO.output(PIN_PULSE, GPIO.LOW)
GPIO.output(PIN_RESULT, GPIO.LOW)

# Load the trained model (Yolov8)
model = YOLO(r"/home/foxrobotics/Documents/PF_Interfaces/best2.pt")

logging.getLogger("ultralytics").setLevel(logging.CRITICAL)

# Start the camera
cap = cv2.VideoCapture(0)

# Function to adjust brightness and contrast
def adjust_brightness_contrast(image, alpha=1.0, beta=20):
    """
    Adjust the brightness and contrast of an image.
    alpha: Contrast factor (>1 increases contrast).
    beta: Brightness value (added to pixel values).
    """
    adjusted = cv2.convertScaleAbs(image, alpha=alpha, beta=beta)
    return adjusted

# Function to handle pin activation
def activate_pins(detection_flag):
    """
    Sends an initial pulse on PIN_PULSE and then sets the state of PIN_RESULT.
    - detection_flag = True (positive): PIN_RESULT -> 1
    - detection_flag = False (negative): PIN_RESULT -> 0
    """

    # Set the state of PIN_RESULT
    if detection_flag:
        GPIO.output(PIN_RESULT, GPIO.HIGH)  # Send 1 in positive case
    else:
        GPIO.output(PIN_RESULT, GPIO.LOW)  # Send 0 in negative case

    GPIO.output(PIN_PULSE, GPIO.HIGH)
    time.sleep(0.1)  # 100ms pulse
    GPIO.output(PIN_PULSE, GPIO.LOW)

while True:
    ret, frame = cap.read()
    if not ret:
        break

    # Read the state of the switch
    switch_state = GPIO.input(SWITCH_PIN)

    # If the switch is pressed (low state), perform three detections
    if switch_state == GPIO.LOW:
        detection_results = []

        for _ in range(1):
            ret, frame = cap.read()
            if not ret:
                break

            # Variable to indicate detection
            detection_flag = False  # Default, nothing detected

            # Implement Object Detection model
            results = model(frame)
            detections = results[0].boxes

            if len(detections) > 0:
                detection_flag = True  # Set to True if detections are present

            # Add the result to the list
            detection_results.append(detection_flag)

            # Draw detected objects
            for box in detections:
                x1, y1, x2, y2 = box.xyxy[0]
                center_x = int((x1 + x2) / 2)
                center_y = int((y1 + y2) / 2)
                cv2.rectangle(frame, (int(x1), int(y1)), (int(x2), int(y2)), (255, 0, 0), 2)
                cv2.circle(frame, (center_x, center_y), 5, (0, 255, 0), -1)
                cv2.putText(frame, f'Detected', (center_x, center_y - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (255, 255, 255), 2)

            # Show processed frame
            cv2.imshow("YOLOv8 Real-Time Detection", frame)
            cv2.waitKey(1000)  # Wait 1 second between detections

        # Print the results of the three detections
        print("Results of the three detections:", detection_results)

        # Send the last result through the pins
        activate_pins(detection_results[-1])

    # Display real-time processed image
    cv2.imshow("YOLOv8 Real-Time Detection", frame)
    
    # Program exit parameter
    if cv2.waitKey(1) & 0xFF == 27:
        break

# Release resources
cap.release()
cv2.destroyAllWindows()
GPIO.cleanup()  # Clean GPIO pin configuration
