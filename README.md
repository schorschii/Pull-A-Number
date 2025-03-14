# Pull A Number
Simple application which prints an ongoing number on a EPSON thermal printer when an Arduino-connected button is pressed.

## Setup
1. Setup your EPSON thermal printer in the CUPS GUI. If connected via serial port, no special driver is needed. Just select "Generic Text-Only Printer".

2. Connect a push button to an Arduino and upload the Sketch `pull_a_number.ino`.

3. Create config file `/etc/pull-a-number.ini`:
   ```
   [arduino]
   serial-port = /dev/pullnumberbutton

   [printer]
   name = EPSON

   [number]
   counter = 1

   [pre-text]
   line1 = ===    Buero 12    ===
   line2 = Ihr Wartenummer lautet:

   [post-text]
   random1 = 640k ought to be enough for anybody
   random2 = new: express shipping for UDP packets
   ```

4. Test if everything works by executing it manually: `python3 pull-a-number.py /etc/pull-a-number.py`.

5. Create systemd service file `/etc/systemd/system/pull-a-number.service`:
   ```
   [Unit]
   Description=Pull a Number

   [Service]
   Type=simple
   Restart=always
   RestartSec=5
   ExecStart=/usr/bin/pull-a-number /etc/pull-a-number.ini

   [Install]
   WantedBy=multi-user.target
   ```
   Enable and start via `systemctl enable pull-a-number.service && systemctl start pull-a-number.service`.

6. (optional) Make a unique device file for the Arduino serial port.  
   If you have multiple usb-serial adapters, you may want to create unique device files as the adapters may be recognized in different order on every boot. In this case, add the following udev rule which makes your Arduino with serial=12345678 always available as /dev/pullnumberbutton (find your serial be reading `dmesg`).
   ```
   SUBSYSTEMS=="usb", KERNEL=="ttyUSB*", ATTRS{idVendor}=="1208", ATTRS{idProduct}=="0780", ATTRS{serial}=="12345678", SYMLINK+="pullnumberbutton"
   ```

## Ideas
- simple web interface with overview of current number
