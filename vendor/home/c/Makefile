# try to do some autodetecting
UNAME := $(shell uname -s)

ifeq "$(UNAME)" "Darwin"
	OS=macosx
endif
ifeq "$(OS)" "Windows_NT"
	OS=windows
endif
ifeq "$(UNAME)" "Linux"
	OS=linux
endif


#################  Mac OS X  ##################################################
ifeq "$(OS)" "macosx"

EXE_SUFFIX=

ARCHS=   -arch i386 -arch x86_64
CFLAGS+= $(ARCHS)
CFLAGS += -mmacosx-version-min=10.6
CFLAGS_MONGOOSE=  -I./mongoose -pthread -g 
LIBS+=	 $(ARCHS)

endif

#################  Windows  ##################################################
ifeq "$(OS)" "windows"

EXE_SUFFIX=.exe

CFLAGS_MONGOOSE = -I./mongoose -mthreads

endif


#################  Common  ##################################################

CFLAGS += $(INCLUDES) -O -Wall -std=gnu99


all: home-task-arduino-serial 

home-task-arduino-serial: home-task.o arduino-serial/arduino-serial-lib.o
	$(CC) $(CFLAGS) -o home-task$(EXE_SUFFIX) home-task.o arduino-serial/arduino-serial-lib.o $(LIBS)

arduino-serial: arduino-serial/arduino-serial.o arduino-serial/arduino-serial-lib.o
	$(CC) $(CFLAGS) -o arduino-serial/arduino-serial$(EXE_SUFFIX) arduino-serial/arduino-serial.o arduino-serial/arduino-serial-lib.o $(LIBS)

arduino-serial-server: arduino-serial/arduino-serial-lib.o
	$(CC) $(CFLAGS) $(CFLAGS_MONGOOSE) -o arduino-serial/arduino-serial-server$(EXE_SUFFIX) arduino-serial/arduino-serial-server.c  arduino-serial/arduino-serial-lib.o arduino-serial/mongoose/mongoose.c $(LIBS)

.c.o:
	$(CC) $(CFLAGS) -c $*.c -o $*.o


clean:
	rm -f $(OBJ) home-task home-task.exe *.o *.a
	rm -f $(OBJ) arduino-serial/arduino-serial arduino-serial/arduino-serial.exe *.o *.a
	rm -f $(OBJ) arduino-serial/arduino-serial-server arduino-serial/arduino-serial-server.exe *.o *.a
	rm -f arduino-serial/mongoose/mongoose.o

