#include <SoftwareSerial.h>

#define DEBUG true

SoftwareSerial esp8266(2,3); // make RX Arduino line is pin 2, make TX Arduino line is pin 3.
                             // This means that you need to connect the TX line from the esp to the Arduino's pin 2
                             // and the RX line from the esp to the Arduino's pin 3
void setup()
{
 
  
  pinMode(10,OUTPUT);
  digitalWrite(10,LOW);
  
  pinMode(11,OUTPUT);
  digitalWrite(11,LOW);
  
  pinMode(12,OUTPUT);
  digitalWrite(12,LOW);
  
  pinMode(13,OUTPUT);
  digitalWrite(13,LOW);

}
boolean EstadoAlarma = true;
int contador = 1;
int tiempo  = 12000;

void loop()
{
    delay(tiempo); 
  if(contador <= 2 ){
      digitalWrite(10, HIGH);
      digitalWrite(11, HIGH);  
      digitalWrite(12, HIGH);
      digitalWrite(13, HIGH);
   contador ++;
   if(EstadoAlarma){
     tiempo = 12000;
     EstadoAlarma = false;
    }
    else{tiempo = 5000;}
  
  }  
  delay(tiempo);  
  digitalWrite(10, LOW);
  digitalWrite(11, LOW);
  digitalWrite(12, LOW);
  digitalWrite(13, LOW);
  tiempo = 15000;
 
  
}
