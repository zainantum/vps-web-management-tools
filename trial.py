import paramiko
import sys
import getopt
import concurrent.futures
import threading


options = ""
command = ""
username = ""
password = ""
ip = ""
platform = ""

def getParam():
    global command, username, password, ip, platform, options
    argv = sys.argv[1:]
    try:
        options, args = getopt.getopt(argv, "f:l:",
                                  ["command=",
                                    "username=",
                                    "password=",
                                    "ip=",
                                    "platform="])
    except:
        print("Error Message ")
    
    for name, value in options:
        if name in ['-c', '--command']:
            command = value
        elif name in ['-u', '--username']:
            username = value
        elif name in ['-p', '--password']:
            password = value
        elif name in ['-i', '--ip']:
            ip = value
        elif name in ['-pl', '--platform']:
            platform = value


def worker(ip):
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        if platform == "do":
            client.connect(ip, username=username, password=password)
        else:
            client.connect(ip, username=username, password=password, key_filename='/var/www/html/exorde/assets/pem/'+username+'.pem')
        # print(commandExec)
        stdin, stdout, stderr = client.exec_command(command)
        stdout=stdout.readlines()
        stdin.close()
        print('<p class="line1">'+ip+'</p>\n<p class="line2">'+str("</p><p class='line2'>".join(stdout))+"</p>")
    except paramiko.AuthenticationException:
        print("Authentication failed, please verify your credentials: %s")
    except paramiko.SSHException as sshException:
        print("Unable to establish SSH connection: %s" % sshException)
    except paramiko.BadHostKeyException as badHostKeyException:
        print("Unable to verify server's host key: %s" % badHostKeyException)
    finally:
        client.close() 


def thread():
    # create a thread pool with 2 threads
    if "," not in ip:
        t1 = threading.Thread(target=worker, args=(ip,))
        t1.start()
        t1.join()
    else:
        countIp = ip.split(",")
        print("<p class='line4'>jumlah ip: "+str(len(countIp))+"</p>")
        for lastIp in countIp:
            # print(lastIp)
            t1 = threading.Thread(target=worker, args=(lastIp,))
            t1.start()
        
        t1.join()


getParam()
thread()
