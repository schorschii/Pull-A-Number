const int buttonPin = 2;
bool pressed = false;

void setup() {
  // put your setup code here, to run once:
  Serial.begin(9600);
  pinMode(buttonPin, INPUT_PULLUP);
}

void loop() {
  if(digitalRead(buttonPin) == LOW) {
    delay(100);
    if(!pressed) {
      pressed = true;
      Serial.println("#");
    }
  } else {
    pressed = false;
  }
}
