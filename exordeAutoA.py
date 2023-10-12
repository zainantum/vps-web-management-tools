import paramiko
import sys


paramIp= sys.argv[1] 
paramUn= sys.argv[2] 
paramPw= sys.argv[3] 


def switch(ip, un, pw):
    try:
        client = paramiko.SSHClient()
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        client.connect(ip, username=un, password=pw)
        if sys.argv[4] == "1":
            #print("Enter all your detail")
            input1 = sys.argv[5] 
            input2 = sys.argv[6] 
            input3 = sys.argv[7] 
            input4 = sys.argv[8] 
            input5 = sys.argv[9] 
            input6 = sys.argv[10] 
            #print("Executing auto run using ssh")
            commandExec = "sudo rm -rf temporaryParam* && sudo wget https://raw.githubusercontent.com/zainantum/exorde-auto/main/temporaryParam.sh && sudo chmod +x * && sudo bash temporaryParam.sh "+input1+" "+str(input2)+" "+input3+" "+input4+" "+input5+" "+input6+" && sudo sysctl vm.swappiness=5"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print("Successfull")
        elif sys.argv[4] == "2":
            commandExec = "sudo zdump /etc/localtime && sudo docker logs -t --tail=2 exorde1 2>&1"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "3":
            commandExec = "sudo df -h"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "4":
            commandExec = "sudo docker ps -a"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "5":
            commandExec = "sudo free -h"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "6":
            commandExec = "sudo docker logs exorde1 -t --tail=5000 2>&1 | grep 'REP'"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "7":
            commandExec = "sudo docker stop exorde1 && sudo docker rm exorde1 && sudo docker rm exorde1"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "8":
            commandExec = "sudo cat /proc/sys/vm/swappiness"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "9":
            commandExec = "sudo sysctl vm.swappiness=5"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "10":
            commandExec = "sudo docker ps -a && sudo docker logs exorde1 2>&1 | grep REPUTATION | wc -l"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "11":
            commandExec = "sudo docker restart exorde1"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "12":
            commandExec = "yes | sudo docker system prune -a && yes | sudo docker image prune && yes | sudo docker rmi $(docker images -a -q) && yes | sudo docker volume prune"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "13":
            commandExec = "sudo rm -rf checkDisk* && sudo wget https://raw.githubusercontent.com/zainantum/exorde-auto/main/checkDisk.sh && sudo chmod +x *"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "14":
            commandExec = "sudo docker image inspect exordelabs/exorde-client --format '{{.RepoDigests}}'"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "15":
            commandExec = "sudo docker stop watchtower && sudo docker rm watchtower && sudo docker run -d --restart unless-stopped --name watchtower -v /var/run/docker.sock:/var/run/docker.sock containrrr/watchtower exorde1 -i 1800 --cleanup"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
        elif sys.argv[4] == "16":
            commandExec = "docker logs exorde1 --since=1h 2>&1 | grep 'Twitter Selenium' | wc -l"
            # print(commandExec)
            stdin, stdout, stderr = client.exec_command(commandExec)
            stdout=stdout.readlines()
            stdin.close()
            print(stdout)
    except paramiko.AuthenticationException:
        print("Authentication failed, please verify your credentials: %s")
    except paramiko.SSHException as sshException:
        print("Unable to establish SSH connection: %s" % sshException)
    except paramiko.BadHostKeyException as badHostKeyException:
        print("Unable to verify server's host key: %s" % badHostKeyException)
    finally:
        client.close() 

        
        
    
#with open('listIp1.txt') as f:
#   for line in f:
#       data = line.split(";")
#       print("Accessing "+str(data[0]))
switch(paramIp,paramUn,paramPw)
