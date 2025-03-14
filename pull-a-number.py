#!/bin/python3

import argparse
import configparser
import serial
import subprocess
import random

ESC = "\x1b"
GS  = "\x1d"
NUL = "\x00"


def printNumber(printerName, preText, number, postText):
    number = str(number)

    # EPSON ESC/POS protocol
    doc  = ESC+'@'
    doc += ESC+'a'+'\x01'  # center
    doc += preText+'\n'
    doc += ESC+'d\x02'  # feed
    doc += ESC+'E\x01'  # bold
    doc += ESC+'!\x38'  # Double height (16) & Double width (32) & Emphasized (8)
    doc += '#'+number
    doc += ESC+'d\x03'  # feed
    doc += GS+'h\x40' + GS+'k\x04'+number+NUL  # barcode
    doc += ESC+'d\x03'  # feed
    doc += ESC+'!\x00'  # normal font
    doc += postText+'\n'
    doc += ESC+'d\x03'  # feed
    doc += GS+'V\x41\x03'  # cut

    lpr = subprocess.Popen(['/usr/bin/lpr', '-P', printerName], stdin=subprocess.PIPE)
    lpr.stdin.write(doc.encode('utf-8'))
    lpr.stdin.close()
    lpr.wait()

def incrementCounter(configFilePath, configParser):
    with open(configFilePath, 'w') as fileHandle:
        configParser.write(fileHandle)

def main():
    parser = argparse.ArgumentParser(add_help=False)
    parser.add_argument('config', type=str)
    args = parser.parse_args()
    configFilePath = args.config

    configParser = configparser.RawConfigParser()
    configParser.read(configFilePath)

    if(not configParser.has_section('arduino')): configParser.add_section('arduino')
    if(not configParser.has_section('printer')): configParser.add_section('printer')
    if(not configParser.has_section('number')): configParser.add_section('number')
    if(not configParser.has_section('pre-text')): configParser.add_section('pre-text')
    if(not configParser.has_section('post-text')): configParser.add_section('post-text')

    buttonSerialPort = configParser['arduino'].get('serial-port', '/dev/ttyACM0')
    buttonSerialBaud = int(configParser['arduino'].get('serial-baud', 9600))
    printerName = configParser['printer'].get('name', 'EPSON')
    counter = int(configParser['number'].get('counter', 0))

    preText = ''
    for key, value in configParser['pre-text'].items():
        preText += value+"\n"

    postTexts = []
    for key, value in configParser['post-text'].items():
        postTexts.append(value)

    s = serial.Serial(buttonSerialPort, baudrate=buttonSerialBaud)
    while True:
        char = s.read()
        if(char == b'#'):
            counter += 1
            print('#', counter, ' -> ', printerName)
            printNumber(printerName, preText.strip(), counter, random.choice(postTexts))
            configParser.set('number', 'counter', counter)
            incrementCounter(configFilePath, configParser)

if(__name__ == '__main__'):
    main()
