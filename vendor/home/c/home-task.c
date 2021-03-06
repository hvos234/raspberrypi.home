#include <stdio.h>    // Standard input/output definitions
#include <signal.h>   // Signal library, to catch Ctr+C and so
#include <stdlib.h>
#include <string.h>   // String function definitions
#include <unistd.h>   // for usleep()
#include <getopt.h>

#include "./arduino-serial/arduino-serial-lib.h"

int _home_task_signal = -1;

//
void home_task_usage(void)
{
    printf("Usage: arduino-serial -b <bps> -p <serialport> [OPTIONS]\n"
    "\n"
    "Options:\n"
    "  -h, --help                 Print this help message\n"
    "  -b, --baud=baudrate        Baudrate (bps) of Arduino (default 9600)\n"
    "  -p, --port=serialport      Serial port Arduino is connected to\n"
    "  -w, --wait                 Wait untill serial port Arduino is connected\n"
    "  -s, --send=string          Send string to Arduino\n"
    //"  -S, --sendline=string      Send string with newline to Arduino\n"
    //"  -i  --stdinput             Use standard input\n"
    "  -c --command               Specify command to run with the return reading\n"
    "  -r, --receive              Receive string from Arduino & print it out\n"
    "  -R, --retry                Retry send x times\n"
    //"  -n  --num=num              Send a number as a single byte\n"
    "  -F  --flush                Flush serial port buffers for fresh reading\n"
    "  -d  --delay=millis         Delay for specified milliseconds\n"
    //"  -e  --eolchar=char         Specify EOL char for reads (default '\\n')\n"
    "  -t  --timeout=millis       Timeout for reads in millisecs (default 5000)\n"
    "  -q  --quiet                Don't print out as much info\n"
    "  -a  --firstchar            Specify char for start reading\n"
    "  -z  --lastchar             Specify char for end reading\n"
    //"  -k  --kill                 Kills al process\n"
    "\n"
    "Note: Order is important. Set '-b' baudrate before opening port'-p'. \n"
    "      Used to make series of actions: '-d 2000 -s hello -d 100 -r' \n"
    "      means 'wait 2secs, send 'hello', wait 100msec, get reply'\n"
    "\n");
}

void home_task_error(char* msg)
{
    fprintf(stderr, "%s\n",msg);
}
            
void home_task_signal(int sig)
{
    _home_task_signal = 0;
    printf("Caught signal %d\n",sig);
}

/*void kill(){
    // The good thing about pgrep is that it will never report itself as a match. But you don't need to get the pid by pgrep and then kill the corresponding process by kill. Use pkill instead
    // see, https://superuser.com/questions/409655/excluding-grep-from-process-list Rockallite
    
    // SIGKILL vs SIGTERM
    // https://major.io/2010/03/18/sigterm-vs-sigkill/
    //pkill -KILL -l 'task'
    //system("pkill -KILL -f task");
    //system("kill -KILL | grep -v \"grep task\" | awk \"{ print \$2 }\"");
}*/

void home_task_command(char* command, char* read, char quiet){
    sprintf(command, "%s \"%s\"", command, read);
    if(!quiet) printf("command %s\n", command);
    system(command);
}

int main(int argc, char *argv[])
{
    const int buf_max = 256;

    int fd = -1;
    char serialport[buf_max];
    int baudrate = 9600;  // default
    char wait=0;
    char quiet=0;
    //char eolchar = '\n';
    char command[(buf_max*2)];
    char a = '^';
    char z = '$';
    int timeout = 5000;
    int retry = 3;
    char buf[buf_max];
    char read[buf_max];
    int rc,n;
    
    if (argc==1) {
        home_task_usage();
        return(1);
    }
    
    // see http://www.yolinux.com/TUTORIALS/C++Signals.html
    signal(SIGINT, home_task_signal); // Program interrupt. (ctrl-c)
    signal(SIGTERM, home_task_signal); // Generated by "kill" command.
    
    /* parse options */
    int option_index = 0, opt;
    static struct option loptions[] = {
        {"help",       no_argument,       0, 'h'},
        {"port",       required_argument, 0, 'p'},
        {"baud",       required_argument, 0, 'b'},
        {"wait",       no_argument,       0, 'w'},
        {"send",       required_argument, 0, 's'},
        //{"sendline",   required_argument, 0, 'S'},
        //{"stdinput",   no_argument,       0, 'i'},
        {"command",    required_argument, 0, 'c'},
        {"receive",    no_argument,       0, 'r'},
        {"retry",      required_argument, 0, 'R'},
        {"flush",      no_argument,       0, 'F'},
        //{"num",        required_argument, 0, 'n'},
        {"delay",      required_argument, 0, 'd'},
        //{"eolchar",    required_argument, 0, 'e'},
        {"firstchar",  required_argument, 0, 'a'},
        {"lastchar",   required_argument, 0, 'z'},
        {"timeout",    required_argument, 0, 't'},
        {"quiet",      no_argument,       0, 'q'},
        //{"kill",       no_argument,       0, 'k'},
        {NULL,         0,                 0, 0}
    };
    
    // kill all other processes
    //kill();
    
    while(1) {
        opt = getopt_long (argc, argv, "hp:b:c:s:rFn:R:d:wqeaz:t:", loptions, &option_index);
        if (opt==-1) break;
        if(_home_task_signal==0) return(0);
        
        switch (opt) {
            case '0': break;
            case 'q':
                quiet = 1;
                break;
            case 'w':
                wait = 1;
                break;
            /*case 'e':
                eolchar = optarg[0];
                if(!quiet) printf("eolchar set to '%c'\n",eolchar);
                break;*/
            case 'a':
                a = optarg[0];
                if(!quiet) printf("first char set to '%c'\n",a);
                break;
            case 'z':
                z = optarg[0];
                if(!quiet) printf("last char set to '%c'\n",z);
                break;
            case 't':
                timeout = strtol(optarg,NULL,10);
                if( !quiet ) printf("timeout set to %d millisecs\n",timeout);
                break;
            case 'R':
                retry = strtol(optarg,NULL,10);
                if( !quiet ) printf("retry set to %d times\n",retry);
                break;
            case 'd':
                n = strtol(optarg,NULL,10);
                if( !quiet ) printf("sleep %d millisecs\n",n);
                usleep(n * 1000 ); // sleep milliseconds
                break;
            case 'h':
                home_task_usage();
                break;
            /*case 'k':
                kill();
                break;*/
            case 'b':
                baudrate = strtol(optarg,NULL,10);
                break;
            case 'p':
                if(!wait) {
                    if( fd!=-1 ) {
                        serialport_close(fd);
                        if(!quiet) printf("closed port %s\n",serialport);
                    }
                    strcpy(serialport,optarg);
                    fd = serialport_init(optarg, baudrate, wait);
                    if( fd==-1 ) {
                        home_task_error("couldn't open port");
                        return(1);
                    }
                    if(!quiet) printf("opened port %s\n",serialport);
                    serialport_flush(fd);
                }else {
                    if( fd!=-1 ) {
                        serialport_close(fd);
                        if(!quiet) printf("closed port %s\n",serialport);
                    }
                    while(1){
                        strcpy(serialport,optarg);
                        fd = serialport_init(optarg, baudrate, wait);
                        if( fd==-1 ) {
                            sleep(30);  // wait 30 seconds try again
                        }else {
                            break;
                        }
                    }
                    if(!quiet) printf("opened port %s\n",serialport);
                    serialport_flush(fd);
                }
                break;
            case 'c':
                strcpy(command,optarg);
                if(!quiet) printf("command is %s\n", command);
                break;
            /*case 'n':
                if( fd == -1 ) home_task_error("serial port not opened");
                n = strtol(optarg, NULL, 10); // convert string to number
                rc = serialport_writebyte(fd, (uint8_t)n);
                if(rc==-1) home_task_error("error writing");
                break;*/
            //case 'S':
            case 's':
                // /var/www/html/home/vendor/home/c/home-task -b 9600 -p /dev/ttyUSB0 -q -R 3 -t 4000 -s "^fr:1;to:4;ac:3$"
                // open
                if( fd == -1 ){
                    home_task_error("serial port not opened");
                    return(1);
                }
                sprintf(buf, (opt=='S' ? "%s\n" : "%s"), optarg);
                
                do {
                    if(_home_task_signal==0) {
                        serialport_close(fd);
                        return(0);
                    }
                    
                    // send
                    if( !quiet ) printf("send string:%s\n", buf);
                    rc = serialport_write(fd, buf);
                    if(rc==-1) {
                        home_task_error("error writing");
                        serialport_close(fd);
                        return(1);
                    }

                    // read
                    memset(read,0,buf_max);  //
                    rc = serialport_read_az(fd, read, buf_max, a, z, timeout);
                    if(rc==-1) retry--;
                    if(rc==0){
                        if( !quiet ) printf("read string:");
                        printf("%s\n", read);
                        if (strlen(command) != 0) home_task_command(command, read, quiet);
                        serialport_close(fd);
                        return(0);
                    }
                    
                } while( retry>0 );
                
                home_task_error("error sending");
                serialport_close(fd);
                return(1);
                break;
            /*case 'i':
                rc=-1;
                if( fd == -1) home_task_error("serial port not opened");
                while(fgets(buf, buf_max, stdin)) {
                    if( !quiet ) printf("send string:%s\n", buf);
                    rc = serialport_write(fd, buf);
                }
                if(rc==-1) home_task_error("error writing");
                break;*/
            case 'r':
                // /var/www/html/home/vendor/home/c/home-task -b 9600 -p /dev/ttyUSB0 -c "/usr/bin/php /var/www/html/home/yii task-receiver" -r
                /*if( fd == -1 ) home_task_error("serial port not opened");
                memset(buf,0,buf_max);  //
                serialport_read_until(fd, buf, eolchar, buf_max, timeout);
                if( !quiet ) printf("read string:");
                printf("%s\n", buf);*/
                
                // read endless
                while(1){
                    if(_home_task_signal==0) return(0);
                    memset(read,0,buf_max);  //
                    rc = serialport_read_az(fd, read, buf_max, a, z, 1800000);
                    if(rc==-9) return(1);
                    if(rc==0){
                        if( !quiet ) printf("read string:");
                        printf("%s\n", read);
                        if (strlen(command) != 0) home_task_command(command, read, quiet);
                    }
                }
                home_task_error("error reading");
                serialport_close(fd);
                return(1);
                break;
            case 'F':
                if( fd == -1 ) {
                    home_task_error("serial port not opened");
                    return(0);
                }
                if( !quiet ) printf("flushing receive buffer\n");
                serialport_flush(fd);
                break;

        }
    }

    // use return instead exit, for proper clean up of the program
    // see https://stackoverflow.com/questions/30250934/how-to-end-c-code 53 and 355
    serialport_close(fd);
    return(0);
} // end main