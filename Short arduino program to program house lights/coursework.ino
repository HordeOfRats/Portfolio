#include <Wire.h>
#include <Adafruit_RGBLCDShield.h>
#include <utility/Adafruit_MCP23017.h>

Adafruit_RGBLCDShield lcd = Adafruit_RGBLCDShield();
#include <EEPROM.h>
#include <LiquidCrystal.h>


#ifdef __arm__
// should use uinstd.h to define sbrk but Due causes a conflict
extern"C" char* sbrk(int incr);
#else// __ARM__
extern char *__brkval;
#endif// __arm__

int freeMemory() {
  char top;
#ifdef __arm__
  return&top - reinterpret_cast<char*>(sbrk(0));
#elif defined(CORE_TEENSY) || (ARDUINO > 103 && ARDUINO != 151)
  return&top - __brkval;
#else// __arm__
  return__brkval ? &top - __brkval : &top - __malloc_heap_start;
#endif// __arm__
}




int eeWriteAddress = 0;
const int numChars = 64;
char receivedChars[numChars];
boolean newValidSerial = false;
boolean tooLong = false;
int lengthOfSerialPort = 0;
int apps = 0;
bool displayInUse = false;
char lcdSelected = 'a';
bool lcdActive = false;
int appsFromEEPROMCount = 0;
int lcdAppIndex = 0;
bool firstS = true;





char tempChar[2];



typedef struct {
  char floor[2];
  char room[2];
  char type[2];
  char name[2];
  char qualifier[2];
  short on_time_hour;
  short on_time_minute;
  short off_time_hour;
  short off_time_minute;
  short temperature;
} Appliance;

Appliance appsFromEEPROM[25];



void setup() {
  // put your setup code here, to run once:
  Serial.begin(9600);
  lcd.begin(16, 2);
  lcd.setCursor(0, 0);
  EEPROMToStructArray();
  Serial.println(F("ENHANCED:LAMP,OUTSIDE,QUERY,MEMORY,SOFT,EEPROM"));





}

void loop() {
  // put your main code here, to run repeatedly:

  readSerialAsCharArray();
  if (newValidSerial == true) {
    Serial.println(receivedChars);
    //Serial.println(lengthOfSerialPort);
    if (receivedChars[0] == 'S') {
      if (firstS == true) {
        clearEEPROM();
        clearEEPROMToStructArray();
        firstS = false;

      }

      parseHouseStringToEEPROM();
    }
    else if (lengthOfSerialPort == 1) {
      if (receivedChars[0] == 'Q') {
        clearEEPROMToStructArray();
        EEPROMToStructArray();
        queryApps();
      }
      else if (receivedChars[0] == 'M') {
        queryMemory();
      }

    }
    for ( int i = 0; i < sizeof(receivedChars);  ++i ) {
      receivedChars[i] = (char)0;
    }
    newValidSerial = false;
    lengthOfSerialPort = 0;

  }
  int butt = lcd.readButtons();
  lcd.setCursor(0, 0);
  lcd.print("select to start");
  lcd.setCursor(0, 1);
  lcd.print("serial will lock");
  if (butt & BUTTON_SELECT) {
    if ((isAlpha(appsFromEEPROM[0].type[0])) == true) {
      lcdActive = true;
      lcd.setCursor(0, 0);
      lcd.print("                ");
      lcd.setCursor(0, 1);
      lcd.print("                ");
      lcdAppIndex = 0;
      clearEEPROMToStructArray();
      EEPROMToStructArray();
    }
    else {
      Serial.println(F("There are no appliances to interact with"));
    }
  }
  while (lcdActive == true) {
    butt = lcd.readButtons();
    int lcdCommand = 0;
    lcd.setCursor(1, 1);
    lcd.print("exit");
    lcd.setCursor(9, 1);
    lcd.print("next");
    lcd.setCursor(1, 0);
    lcd.print(appsFromEEPROM[lcdAppIndex].floor[0]);
    lcd.print(appsFromEEPROM[lcdAppIndex].room[0]);
    lcd.print(appsFromEEPROM[lcdAppIndex].type[0]);
    if ((appsFromEEPROM[lcdAppIndex].name[0]) == 'N') {
      lcd.print("M");
    }
    else {
      lcd.print(appsFromEEPROM[lcdAppIndex].name[0]);
    }
    if ((appsFromEEPROM[lcdAppIndex].qualifier[0]) != 'N') {
      lcd.print(appsFromEEPROM[lcdAppIndex].qualifier[0]);
    }
    switch (lcdSelected) {
      case 'a':
        lcd.setCursor(8, 1);
        lcd.print(" ");
        lcd.setCursor(0, 1);
        lcd.print(" ");
        lcd.setCursor(0, 0);
        lcd.print("@");
        break;
      case 'b':
        lcd.setCursor(8, 1);
        lcd.print(" ");
        lcd.setCursor(0, 0);
        lcd.print(" ");
        lcd.setCursor(0, 1);
        lcd.print("@");
        break;
      case 'c':
        lcd.setCursor(0, 1);
        lcd.print(" ");
        lcd.setCursor(0, 0);
        lcd.print(" ");
        lcd.setCursor(8, 1);
        lcd.print("@");
    }
    if (butt & BUTTON_LEFT) {
      if (lcdSelected == 'b') {
        lcdSelected = 'c';
      }
      else if (lcdSelected == 'c') {
        lcdSelected = 'b';
      }
      littleWait();
    }
    else if (butt & BUTTON_UP) {
      if (lcdSelected == 'b') {
        lcdSelected = 'a';
      }
      else if (lcdSelected == 'c') {
        lcdSelected = 'a';
      }
      littleWait();
    }
    else if (butt & BUTTON_RIGHT) {
      if (lcdSelected == 'b') {
        lcdSelected = 'c';
      }
      else if (lcdSelected == 'c') {
        lcdSelected = 'b';
      }
      littleWait();
    }
    else if (butt & BUTTON_DOWN) {
      if (lcdSelected == 'a') {
        lcdSelected = 'b';
      }
      littleWait();
    }
    else if  (butt & BUTTON_SELECT) {
      if (lcdSelected == 'a') {
        lcdCommand = 1;
      }
      else if (lcdSelected == 'b') {
        lcdActive = false;
        appArraytoEEPROM();
        emptySerialBuffer();
        butt = -1;
      }
      else if (lcdSelected == 'c') {
        lcd.setCursor(1, 0);
        lcd.print("               ");
        lcdAppIndex = lcdAppIndex + 1;
        if (lcdAppIndex == appsFromEEPROMCount) {
          lcdAppIndex = 0;
        }

      }
      littleWait();

    }

    if (lcdCommand == 1) {
      lcd.setCursor(0, 0);
      lcd.print("                ");
      lcd.setCursor(0, 1);
      lcd.print("                ");
      if (appsFromEEPROM[lcdAppIndex].type[0] == 'H') {
        lcdCommand = 2;
      }
      else {
        lcdCommand = 3;
      }

    }
    while (lcdCommand == 2) {
      butt = lcd.readButtons();
      lcd.setCursor(0, 0);
      lcd.print("set level: ");
      lcd.print(appsFromEEPROM[lcdAppIndex].temperature);
      if (butt & BUTTON_UP) {
        appsFromEEPROM[lcdAppIndex].temperature = appsFromEEPROM[lcdAppIndex].temperature + 1;
        if ((appsFromEEPROM[lcdAppIndex].temperature) == 101) {
          appsFromEEPROM[lcdAppIndex].temperature = 1;
        }
        lcd.setCursor(11, 0);
        lcd.print("     ");
        lcd.print(appsFromEEPROM[lcdAppIndex].temperature);
        littleWait();
      }
      else if (butt & BUTTON_DOWN) {
        appsFromEEPROM[lcdAppIndex].temperature = appsFromEEPROM[lcdAppIndex].temperature - 1;
        if ((appsFromEEPROM[lcdAppIndex].temperature) == 0) {
          appsFromEEPROM[lcdAppIndex].temperature = 100;
        }
        lcd.setCursor(11, 0);
        lcd.print("     ");
        lcd.print(appsFromEEPROM[lcdAppIndex].temperature);
        littleWait();
      }
      else if (butt & BUTTON_SELECT) {
        lcd.setCursor(0, 0);
        lcd.print("                ");
        lcd.setCursor(0, 1);
        lcd.print("                ");
        lcdCommand = 0;
        littleWait();
      }

    }
    while (lcdCommand == 3) {
      butt = lcd.readButtons();
      int offOrOn = 0;
      lcd.setCursor(0, 0);
      lcd.print("up for time on");
      lcd.setCursor(0, 1);
      lcd.print("dwn for time off");
      if (butt & BUTTON_UP) {
        offOrOn = 1;
        lcd.setCursor(0, 0);
        lcd.print("                ");
        lcd.setCursor(0, 1);
        lcd.print("                ");
        lcd.setCursor(0, 0);
        lcd.print("u/d=hour");
        lcd.setCursor(0, 1);
        lcd.print("l/r=min sel=exit");
        lcd.setCursor(11, 0);
        lcd.print("     ");
        lcd.setCursor(11, 0);
        lcd.print(appsFromEEPROM[lcdAppIndex].on_time_hour);
        lcd.print(":");
        lcd.print(appsFromEEPROM[lcdAppIndex].on_time_minute);
        littleWait();
      }
      else if (butt & BUTTON_DOWN) {
        offOrOn = 2;
        lcd.setCursor(0, 0);
        lcd.print("                ");
        lcd.setCursor(0, 1);
        lcd.print("                ");
        lcd.setCursor(0, 0);
        lcd.print("u/d=hour");
        lcd.setCursor(0, 1);
        lcd.print("l/r=min sel=exit");
        lcd.setCursor(11, 0);
        lcd.print("     ");
        lcd.setCursor(11, 0);
        lcd.print(appsFromEEPROM[lcdAppIndex].off_time_hour);
        lcd.print(":");
        lcd.print(appsFromEEPROM[lcdAppIndex].off_time_minute);
        littleWait();
      }
      while (offOrOn == 1) {
        butt = lcd.readButtons();
        if (butt & BUTTON_UP) {
          appsFromEEPROM[lcdAppIndex].on_time_hour = appsFromEEPROM[lcdAppIndex].on_time_hour + 1;
          if ((appsFromEEPROM[lcdAppIndex].on_time_hour) == 25) {
            appsFromEEPROM[lcdAppIndex].on_time_hour = 1;
          }
          lcd.setCursor(11, 0);
          lcd.print("     ");
          lcd.setCursor(11, 0);
          lcd.print(appsFromEEPROM[lcdAppIndex].on_time_hour);
          lcd.print(":");
          lcd.print(appsFromEEPROM[lcdAppIndex].on_time_minute);
          littleWait();

        }
        else if (butt & BUTTON_DOWN) {
          appsFromEEPROM[lcdAppIndex].on_time_hour = appsFromEEPROM[lcdAppIndex].on_time_hour - 1;
          if ((appsFromEEPROM[lcdAppIndex].on_time_hour) == 0) {
            appsFromEEPROM[lcdAppIndex].on_time_hour = 24;
          }
          lcd.setCursor(11, 0);
          lcd.print("     ");
          lcd.setCursor(11, 0);
          lcd.print(appsFromEEPROM[lcdAppIndex].on_time_hour);
          lcd.print(":");
          lcd.print(appsFromEEPROM[lcdAppIndex].on_time_minute);
          littleWait();
        }
        else if (butt & BUTTON_RIGHT) {
          appsFromEEPROM[lcdAppIndex].on_time_minute = appsFromEEPROM[lcdAppIndex].on_time_minute + 1;
          if ((appsFromEEPROM[lcdAppIndex].on_time_minute) == 61) {
            appsFromEEPROM[lcdAppIndex].on_time_minute = 0;
          }
          lcd.setCursor(11, 0);
          lcd.print("     ");
          lcd.setCursor(11, 0);
          lcd.print(appsFromEEPROM[lcdAppIndex].on_time_hour);
          lcd.print(":");
          lcd.print(appsFromEEPROM[lcdAppIndex].on_time_minute);
          littleWait();
        }
        else if (butt & BUTTON_LEFT) {
          appsFromEEPROM[lcdAppIndex].on_time_minute = appsFromEEPROM[lcdAppIndex].on_time_minute - 1;
          if ((appsFromEEPROM[lcdAppIndex].on_time_minute) == -1) {
            appsFromEEPROM[lcdAppIndex].on_time_minute = 60;
          }

          lcd.setCursor(11, 0);
          lcd.print("     ");
          lcd.setCursor(11, 0);
          lcd.print(appsFromEEPROM[lcdAppIndex].on_time_hour);
          lcd.print(":");
          lcd.print(appsFromEEPROM[lcdAppIndex].on_time_minute);
          littleWait();
        }

        else if (butt & BUTTON_SELECT) {
          lcd.setCursor(0, 0);
          lcd.print("                ");
          lcd.setCursor(0, 1);
          lcd.print("                ");
          offOrOn = 0;
          lcdCommand = 0;
          littleWait();
        }


      }
      while (offOrOn == 2) {
        //Serial.println(freeMemory());
        butt = lcd.readButtons();
        if (butt & BUTTON_UP) {
          appsFromEEPROM[lcdAppIndex].off_time_hour = appsFromEEPROM[lcdAppIndex].off_time_hour + 1;
          if ((appsFromEEPROM[lcdAppIndex].off_time_hour) == 25) {
            appsFromEEPROM[lcdAppIndex].off_time_hour = 1;
          }
          lcd.setCursor(11, 0);
          lcd.print("     ");
          lcd.setCursor(11, 0);
          lcd.print(appsFromEEPROM[lcdAppIndex].off_time_hour);
          lcd.print(":");
          lcd.print(appsFromEEPROM[lcdAppIndex].off_time_minute);
          littleWait();

        }
        else if (butt & BUTTON_DOWN) {
          appsFromEEPROM[lcdAppIndex].off_time_hour = appsFromEEPROM[lcdAppIndex].off_time_hour - 1;
          if ((appsFromEEPROM[lcdAppIndex].off_time_hour) == 0) {
            appsFromEEPROM[lcdAppIndex].off_time_hour = 24;
          }
          lcd.setCursor(11, 0);
          lcd.print("     ");
          lcd.setCursor(11, 0);
          lcd.print(appsFromEEPROM[lcdAppIndex].off_time_hour);
          lcd.print(":");
          lcd.print(appsFromEEPROM[lcdAppIndex].off_time_minute);
          littleWait();
        }
        else if (butt & BUTTON_RIGHT) {
          appsFromEEPROM[lcdAppIndex].off_time_minute = appsFromEEPROM[lcdAppIndex].off_time_minute + 1;
          if ((appsFromEEPROM[lcdAppIndex].off_time_minute) == 61) {
            appsFromEEPROM[lcdAppIndex].off_time_minute = 0;
          }
          lcd.setCursor(11, 0);
          lcd.print("     ");
          lcd.setCursor(11, 0);
          lcd.print(appsFromEEPROM[lcdAppIndex].off_time_hour);
          lcd.print(":");
          lcd.print(appsFromEEPROM[lcdAppIndex].off_time_minute);
          littleWait();
        }
        else if (butt & BUTTON_LEFT) {
          appsFromEEPROM[lcdAppIndex].off_time_minute = appsFromEEPROM[lcdAppIndex].off_time_minute - 1;
          if ((appsFromEEPROM[lcdAppIndex].off_time_minute) == -1) {
            appsFromEEPROM[lcdAppIndex].off_time_minute = 60;
          }
          lcd.setCursor(11, 0);
          lcd.print("     ");
          lcd.setCursor(11, 0);
          lcd.print(appsFromEEPROM[lcdAppIndex].off_time_hour);
          lcd.print(":");
          lcd.print(appsFromEEPROM[lcdAppIndex].off_time_minute);
          littleWait();
        }

        else if (butt & BUTTON_SELECT) {
          lcd.setCursor(0, 0);
          lcd.print("                ");
          lcd.setCursor(0, 1);
          lcd.print("                ");
          offOrOn = 0;
          lcdCommand = 0;
          littleWait();
        }


      }


    }

  }
}
void queryMemory() {
  Serial.println("Free memory is ");
  Serial.println(freeMemory());
  Serial.println(F("Also, there are"));
  Serial.println(appsFromEEPROMCount);
  Serial.println(F("appliances currently in use out of 25"));
}




void queryApps() {
  Serial.println("There are");
  Serial.println(appsFromEEPROMCount);
  Serial.println("appliances in use");
  for ( int z = 0; z < 25;  z = z + 1 ) {
    if ((isAlpha(appsFromEEPROM[z].type[0])) == true) {
      if ((appsFromEEPROM[z].type[0]) == 'H') {
        switch (appsFromEEPROM[z].floor[0]) {
          case 'G':
            Serial.print(F("Ground"));
            break;
          case 'F':
            Serial.print(F("First"));
            break;
          case 'O':
            Serial.print(F("Outside"));
            break;


        }
        Serial.print("/");


        switch (appsFromEEPROM[z].room[0]) {
          case 'K':
            Serial.print(F("Kitchen"));
            break;
          case 'B':
            Serial.print(F("Bathroom"));
            break;
          case 'L':
            Serial.print(F("Lounge"));
            break;
          case '1':
            Serial.print(F("Bedroom_1"));
            break;
          case '2':
            Serial.print(F("Bedroom_2"));
            break;
          case '3':
            Serial.print("Bedroom_3");
            break;
          case '4':
            Serial.print("Bedroom_4");
            break;
          case 'G':
            Serial.print("Garage");
            break;
          case 'R':
            Serial.print("Garden");
            break;
          case 'P':
            Serial.print("Play_Room");
            break;
          case 'H':
            Serial.print("Hall");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].type[0]) {
          case 'L':
            Serial.print("Light");
            break;
          case 'A':
            Serial.print("Lamp");
            break;
          case 'H':
            Serial.print("Heat");
            break;
          case 'W':
            Serial.print("Water");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].name[0]) {
          case 'M':
            Serial.print("Main");
            break;
          case 'C':
            Serial.print("Ceiling");
            break;
          case 'D':
            Serial.print("Desk");
            break;
          case 'B':
            Serial.print("Bed");
            break;
          case 'P':
            Serial.print("Cupboard");
            break;
          case 'W':
            Serial.print("Wall");
            break;
          default:
            Serial.print("Main");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].qualifier[0]) {
          case '1':
            Serial.print("One");
            Serial.print("/");
            break;
          case '2':
            Serial.print("Two");
            Serial.print("/");
            break;
          case '3':
            Serial.print("Three");
            Serial.print("/");
            break;
          case 'L':
            Serial.print("Left");
            Serial.print("/");
            break;
          case 'R':
            Serial.print("Right");
            Serial.print("/");
            break;

        }
        Serial.print("Level:");
        Serial.println(appsFromEEPROM[z].temperature);



      }
      else {
        switch (appsFromEEPROM[z].floor[0]) {
          case 'G':
            Serial.print("Ground");
            break;
          case 'F':
            Serial.print("First");
            break;
          case 'O':
            Serial.print("Outside");
            break;


        }
        Serial.print("/");


        switch (appsFromEEPROM[z].room[0]) {
          case 'K':
            Serial.print("Kitchen");
            break;
          case 'B':
            Serial.print("Bathroom");
            break;
          case 'L':
            Serial.print("Lounge");
            break;
          case '1':
            Serial.print("Bedroom_1");
            break;
          case '2':
            Serial.print("Bedroom_2");
            break;
          case '3':
            Serial.print("Bedroom_3");
            break;
          case '4':
            Serial.print("Bedroom_4");
            break;
          case 'G':
            Serial.print("Garage");
            break;
          case 'R':
            Serial.print("Garden");
            break;
          case 'P':
            Serial.print("Play_Room");
            break;
          case 'H':
            Serial.print("Hall");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].type[0]) {
          case 'L':
            Serial.print("Light");
            break;
          case 'A':
            Serial.print("Lamp");
            break;
          case 'H':
            Serial.print("Heat");
            break;
          case 'W':
            Serial.print("Water");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].name[0]) {
          case 'M':
            Serial.print("Main");
            break;
          case 'C':
            Serial.print("Ceiling");
            break;
          case 'D':
            Serial.print("Desk");
            break;
          case 'B':
            Serial.print("Bed");
            break;
          case 'P':
            Serial.print("Cupboard");
            break;
          case 'W':
            Serial.print("Wall");
            break;
          default:
            Serial.print("Main");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].qualifier[0]) {
          case '1':
            Serial.print("One");
            Serial.print("/");
            break;
          case '2':
            Serial.print("Two");
            Serial.print("/");
            break;
          case '3':
            Serial.print("Three");
            Serial.print("/");
            break;
          case 'L':
            Serial.print("Left");
            Serial.print("/");
            break;
          case 'R':
            Serial.print("Right");
            Serial.print("/");
            break;

        }
        Serial.print("On:");
        Serial.print(appsFromEEPROM[z].on_time_hour);
        Serial.print(":");
        Serial.println(appsFromEEPROM[z].on_time_minute);

        switch (appsFromEEPROM[z].floor[0]) {
          case 'G':
            Serial.print("Ground");
            break;
          case 'F':
            Serial.print("First");
            break;
          case 'O':
            Serial.print("Outside");
            break;


        }
        Serial.print("/");


        switch (appsFromEEPROM[z].room[0]) {
          case 'K':
            Serial.print("Kitchen");
            break;
          case 'B':
            Serial.print("Bathroom");
            break;
          case 'L':
            Serial.print("Lounge");
            break;
          case '1':
            Serial.print("Bedroom_1");
            break;
          case '2':
            Serial.print("Bedroom_2");
            break;
          case '3':
            Serial.print("Bedroom_3");
            break;
          case '4':
            Serial.print("Bedroom_4");
            break;
          case 'G':
            Serial.print("Garage");
            break;
          case 'R':
            Serial.print("Garden");
            break;
          case 'P':
            Serial.print("Play_Room");
            break;
          case 'H':
            Serial.print("Hall");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].type[0]) {
          case 'L':
            Serial.print("Light");
            break;
          case 'A':
            Serial.print("Lamp");
            break;
          case 'H':
            Serial.print("Heat");
            break;
          case 'W':
            Serial.print("Water");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].name[0]) {
          case 'M':
            Serial.print("Main");
            break;
          case 'C':
            Serial.print("Ceiling");
            break;
          case 'D':
            Serial.print("Desk");
            break;
          case 'B':
            Serial.print("Bed");
            break;
          case 'P':
            Serial.print("Cupboard");
            break;
          case 'W':
            Serial.print("Wall");
            break;
          default:
            Serial.print("Main");
            break;

        }
        Serial.print("/");

        switch (appsFromEEPROM[z].qualifier[0]) {
          case '1':
            Serial.print("One");
            Serial.print("/");
            break;
          case '2':
            Serial.print("Two");
            Serial.print("/");
            break;
          case '3':
            Serial.print("Three");
            Serial.print("/");
            break;
          case 'L':
            Serial.print("Left");
            Serial.print("/");
            break;
          case 'R':
            Serial.print("Right");
            Serial.print("/");
            break;

        }
        Serial.print("Off:");
        Serial.print(appsFromEEPROM[z].off_time_hour);
        Serial.print(":");
        Serial.println(appsFromEEPROM[z].off_time_minute);

      }
    }
  }


}

void clearEEPROMToStructArray() {
  memset(appsFromEEPROM, 0, sizeof(appsFromEEPROM));
}




void EEPROMToStructArray() {
  Appliance readingEEPROM;
  appsFromEEPROMCount = 0;
  for ( int i = 0; i < 500;  i = i + 20 ) {
    EEPROM.get(i, readingEEPROM);
    if ((isAlpha(readingEEPROM.type[0])) == true) {
      appsFromEEPROM[appsFromEEPROMCount] = readingEEPROM;
      //Serial.println(appsFromEEPROM[appsFromEEPROMCount].room);
      appsFromEEPROMCount = appsFromEEPROMCount + 1;
    }
    else {
      break;
    }



  }

}





void appArraytoEEPROM() {
  eeWriteAddress = 0;
  clearEEPROM();
  for ( int u = 0; u < (appsFromEEPROMCount); u = u + 1) {
    EEPROM.put(eeWriteAddress, appsFromEEPROM[u]);
    eeWriteAddress = eeWriteAddress + (sizeof(Appliance));

  }



}







void readSerialAsCharArray() {
  static int serialIndex = 0;
  char readCharacter;


  while (Serial.available() > 0 && newValidSerial == false && tooLong == false) {
    readCharacter = Serial.read();

    if (Serial.available() > 0) {
      receivedChars[serialIndex] = readCharacter;
      //Serial.println(receivedChars);
      serialIndex++;
      if (serialIndex >= (numChars - 1)) {
        tooLong = true;
      }
      tinyWait();
    }
    else {
      tinyWait();
      if (Serial.available() == 0) {

        receivedChars[serialIndex] = '\0'; // terminate the string
        lengthOfSerialPort = serialIndex;
        serialIndex = 0;
        newValidSerial = true;
      }
    }
    if (tooLong == true) {
      for ( int i = 0; i < sizeof(receivedChars);  ++i ) {
        receivedChars[i] = (char)0;
      }
      newValidSerial = false;
      serialIndex = 0;
      tooLong = false;
      emptySerialBuffer();
      Serial.println(F("The data entered in the serial port is too long and has been discarded."));
    }
  }
  if (lengthOfSerialPort > 0) {
    Serial.println("Serial read finished");
  }

}

void parseHouseStringToEEPROM() {
  Appliance writeAppToEEPROM;
  bool waitForComma = false;
  bool upToType = false;
  bool upToName = false;
  bool upToQualifier = false;
  bool validApplianceData = false;
  //Serial.println(sizeof(receivedChars));
  for ( int i = 1; i < lengthOfSerialPort - 2;  i = i + 1 ) {
    //Serial.println(lengthOfSerialPort);
    if (waitForComma == false) {
      if (apps < 25) {
        //Serial.println(receivedChars[i]);
        if (receivedChars[i] != ',' && receivedChars[i] != '\n') {
          if (receivedChars[i] == 'G' || receivedChars[i] == 'F') {
            if (receivedChars[i + 1] == 'K' || receivedChars[i + 1] == 'B' || receivedChars[i + 1] == 'L' || receivedChars[i + 1] == '1' || receivedChars[i + 1] == '2' || receivedChars[i + 1] == '3' || receivedChars[i + 1] == '4' || receivedChars[i + 1] == 'P' || receivedChars[i + 1] == 'H') {
              if (receivedChars[i + 2] == 'L' || receivedChars[i + 2] == 'A' || receivedChars[i + 2] == 'H') {
                upToType = true;
                if (receivedChars[i + 3] == 'M' || receivedChars[i + 3] == 'C' || receivedChars[i + 3] == 'D' || receivedChars[i + 3] == 'B' || receivedChars[i + 3] == 'P' || receivedChars[i + 3] == 'W' ) {
                  upToName = true;
                  if (receivedChars[i + 4] == '1' || receivedChars[i + 4] == '2' || receivedChars[i + 4] == '3' || receivedChars[i + 4] == 'L' || receivedChars[i + 4] == 'R') {
                    upToQualifier = true;
                    if (receivedChars[i + 5] == ',' || receivedChars[i + 5] == '.' || receivedChars[i + 5] == '\n') {
                      validApplianceData = true;
                    }
                  }
                }
              }
            }

          }

          else if (receivedChars[i] == 'O') {
            //Serial.println("test1");
            if (receivedChars[i + 1] == 'G') {
              //Serial.println("test2");
              if (receivedChars[i + 2] == 'L' || receivedChars[i + 2] == 'A' || receivedChars[i + 2] == 'H' || receivedChars[i + 2] == 'W') {
                //Serial.println("test3");
                upToType = true;
                if (receivedChars[i + 3] == 'M' || receivedChars[i + 3] == 'C' || receivedChars[i + 3] == 'D' || receivedChars[i + 3] == 'B' || receivedChars[i + 3] == 'P' || receivedChars[i + 3] == 'W' ) {
                  upToName = true;
                  if (receivedChars[i + 4] == '1' || receivedChars[i + 4] == '2' || receivedChars[i + 4] == '3' || receivedChars[i + 4] == 'L' || receivedChars[i + 4] == 'R') {
                    upToQualifier = true;
                    if (receivedChars[i + 5] == ',' || receivedChars[i + 5] == '.' || receivedChars[i + 5] == '\n') {
                      validApplianceData = true;
                    }
                  }

                }
              }

            }


            else if (receivedChars[i + 1] == 'R') {
              if (receivedChars[i + 2] == 'L' || receivedChars[i + 2] == 'H' || receivedChars[i + 2] == 'W') {
                upToType = true;
                if (receivedChars[i + 3] == 'M' || receivedChars[i + 3] == 'C' || receivedChars[i + 3] == 'W') {
                  upToName = true;
                  //Serial.println("test orange");
                  if (receivedChars[i + 4] == '1' || receivedChars[i + 4] == '2' || receivedChars[i + 4] == '3' || receivedChars[i + 4] == 'L' || receivedChars[i + 4] == 'R') {
                    upToQualifier = true;
                    if (receivedChars[i + 5] == ',' || receivedChars[i + 5] == '.' || receivedChars[i + 5] == '\n') {
                      validApplianceData = true;
                    }
                  }
                }

              }

            }
          }


          if (validApplianceData == false) {
            //Serial.println ("test4");
            if (upToName == true) {
              if (receivedChars[i + 4] == ',' || receivedChars[i + 4] == '.' || receivedChars[i + 4] == '\n') {
                validApplianceData = true;
                //Serial.println("test9");
              }
            }
            else if (upToType == true) {
              //Serial.println("test5");
              if (receivedChars[i + 3] == ',' || receivedChars[i + 3] == '.' || receivedChars[i + 3] == '\n') {
                validApplianceData = true;
                //Serial.println("test6");
              }
            }
          }

          if (validApplianceData == true) {
            char tempChar[2];
            tempChar[1] = '\0';

            tempChar[0] = receivedChars[i];
            writeAppToEEPROM.floor[0] = tempChar[0];
            writeAppToEEPROM.floor[1] = tempChar[1];

            tempChar[0] = receivedChars[i + 1];
            writeAppToEEPROM.room[0] = tempChar[0];
            writeAppToEEPROM.room[1] = tempChar[1];

            tempChar[0] = receivedChars[i + 2];
            writeAppToEEPROM.type[0] = tempChar[0];
            writeAppToEEPROM.type[1] = tempChar[1];

            if (upToName == true) {

              tempChar[0] = receivedChars[i + 3];
              writeAppToEEPROM.name[0] = tempChar[0];
              writeAppToEEPROM.name[1] = tempChar[1];
            }
            else {

              writeAppToEEPROM.name[0] = 'N';
              writeAppToEEPROM.name[1] = tempChar[1];
            }
            if (upToQualifier == true) {

              tempChar[0] = receivedChars[i + 4];
              writeAppToEEPROM.qualifier[0] = tempChar[0];
              writeAppToEEPROM.qualifier[1] = tempChar[1];
            }
            else {
              writeAppToEEPROM.qualifier[0] = 'N';
              writeAppToEEPROM.qualifier[1] = tempChar[1];
            }
            writeAppToEEPROM.on_time_hour = 8;
            writeAppToEEPROM.on_time_minute = 30;
            writeAppToEEPROM.off_time_hour = 20;
            writeAppToEEPROM.off_time_minute = 30;
            writeAppToEEPROM.temperature = 50;
            Appliance readFromEEPROM;
            //Serial.println("testing cherry");



            for (int j = 0; j < 25;  ++j ) {
              if ((memcmp (&appsFromEEPROM[j], &writeAppToEEPROM, 20)) == 0) {
                validApplianceData = false;
                Serial.println("duplicate detected");
              }
            }
            if (validApplianceData == true) {
              //Serial.println("testingbanana");

              EEPROM.put(eeWriteAddress, writeAppToEEPROM);
              //EEPROM.get(eeWriteAddress, readFromEEPROM);
              eeWriteAddress += sizeof(Appliance);
              //Serial.println(readFromEEPROM.room);
              upToType = false;
              upToName = false;
              upToQualifier = false;
              validApplianceData = false;
              waitForComma = true;
              apps = apps + 1;
              clearEEPROMToStructArray();
              EEPROMToStructArray();
            }
          }
          if (validApplianceData == false) {
            upToType = false;
            upToName = false;
            upToQualifier = false;
            validApplianceData = false;
            waitForComma = true;
            if (receivedChars[i] == ',' || receivedChars[i] == '\n') {
              waitForComma = false;
            }

          }
          if (receivedChars[i] == '.' ) {
            for ( int i = 0; i < sizeof(receivedChars);  ++i ) {
              receivedChars[i] = (char)0;
            }

            break;
          }

        }
        else {
          //Serial.println("comma wait");

        }
      }
    }
    else {
      if (receivedChars[i] == ',' || receivedChars[i] == '\n') {
        waitForComma = false;
      }
      else if (receivedChars[i] == '.' ) {
        for ( int i = 0; i < sizeof(receivedChars);  ++i ) {
          receivedChars[i] = (char)0;
        }

        break;
      }



    }
  }
}










void littleWait() {
  unsigned long startOfWait = millis();
  while (millis() < (startOfWait + 200)) {

  }
}

void longWait() {
  unsigned long startOfLWait = millis();
  while (millis() < (startOfLWait + 1000)) {

  }
}

void tinyWait() {
  unsigned long startOfTWait = millis();
  while (millis() < (startOfTWait + 50)) {

  }
}





void emptySerialBuffer() {
  while (Serial.available() > 0) {
    char t = Serial.read();
  }
}
void clearEEPROM() {
  for (int i = 0 ; i < EEPROM.length() ; i++) {
    EEPROM.write(i, 0);
  }
}
