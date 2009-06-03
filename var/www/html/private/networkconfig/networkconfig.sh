#!/bin/sh

# Davide.Gustin by SpheraIT

hostnamevar=$1
ipvar=$2
netmaskvar=$3
gatewayvar=$4
haipvar=$5
gatewayswitchvar=$6

if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ] || [ -z "$4" ] || [ -z "$5" ] || [ -z "$6" ]; then
  echo "usage: networkconfig [hostname] [ip] [netmask] [gateway] [haip (off) for disable] [switch]"
  echo
  echo "switch = type 1 for generate the GATEWAY settings in this file: /etc/sysconfig/network-scripts/ifcfg-eth0"
  echo "         type 0 for generate the GATEWAY settings in this file: /etc/sysconfig/network"
  echo
  exit 1
fi

# Config Files

eth0conf="/etc/sysconfig/network-scripts/ifcfg-eth0"
eth0haconf="/etc/sysconfig/network-scripts/ifcfg-eth0:0"
networkconf="/etc/sysconfig/network"
hostsconf="/etc/hosts"
haresourceconf="/etc/ha.d/haresources"

# Delete a single line.

dellocalhost=`sed -i -r "/^127.0.0.1/d" $hostsconf`

# Execute a command

network=`ipcalc -n $ipvar -p $netmaskvar | grep -i NETWORK | cut -d= -f2 | awk '{print $1}'`
prefix=`ipcalc -n $ipvar -p $netmaskvar | grep -i PREFIX | cut -d= -f2 | awk '{print $1}'`
broadcast=`ipcalc -b $ipvar -p $netmaskvar | grep -i BROADCAST | cut -d= -f2 | awk '{print $1}'`
hwaddr=`ifconfig eth0 | grep HWaddr | sed -e "s/^.*HWaddr //" | sed -e "s/ //g"`

#

if [ "$gatewayswitchvar" == "0" ]; then

# eth0

    echo "BOOTPROTO=static" > $eth0conf
    echo "HWADDR=$hwaddr" >> $eth0conf
    echo "TYPE=Ethernet" >> $eth0conf
    echo "DEVICE=eth0" >> $eth0conf
    echo "NETMASK=$netmaskvar" >> $eth0conf
    echo "BROADCAST=$broadcast" >> $eth0conf
    echo "IPADDR=$ipvar" >> $eth0conf
    echo "NETWORK=$network" >> $eth0conf
    echo "ONBOOT=yes" >> $eth0conf

# eth0:0

    if [ "$haipvar" != "off" ]; then

        echo "BOOTPROTO=static" > $eth0haconf
        echo "TYPE=Ethernet" >> $eth0haconf
        echo "DEVICE=eth0:0" >> $eth0haconf
        echo "NETMASK=$netmaskvar" >> $eth0haconf
        echo "BROADCAST=$broadcast" >> $eth0haconf
        echo "IPADDR=$haipvar" >> $eth0haconf
        echo "NETWORK=$network" >> $eth0haconf
        echo "ONBOOT=no" >> $eth0haconf
        echo "ONPARENT=no" >> $eth0haconf

        if grep -q "0103" /etc/voiper_pn; then

            echo "voiper-gw01 $haipvar/$prefix/eth0:0/$broadcast cluster MailTo::root@localhost" > $haresourceconf

        else
        
                echo "voiper-sip01 $haipvar/$prefix/eth0:0/$broadcast portmap nfs cluster MailTo::root@localhost" > $haresourceconf

        fi

    fi

#
    echo "GATEWAY=$gatewayvar" > $networkconf
    echo "NETWORKING=yes" >> $networkconf
    echo "HOSTNAME=$hostnamevar" >> $networkconf



fi

if [ "$gatewayswitchvar" == "1" ]; then

# eth0

    echo "BOOTPROTO=static" > $eth0conf
    echo "HWADDR=$hwaddr" >> $eth0conf
    echo "TYPE=Ethernet" >> $eth0conf
    echo "DEVICE=eth0" >> $eth0conf
    echo "NETMASK=$netmaskvar" >> $eth0conf
    echo "BROADCAST=$broadcast" >> $eth0conf
    echo "IPADDR=$ipvar" >> $eth0conf
    echo "NETWORK=$network" >> $eth0conf
    echo "ONBOOT=yes" >> $eth0conf
    echo "GATEWAY=$gatewayvar" >> $eth0conf

# eth0:0

    if [ "$haipvar" != "off" ]; then

        echo "BOOTPROTO=static" > $eth0haconf
        echo "TYPE=Ethernet" >> $eth0haconf
        echo "DEVICE=eth0:0" >> $eth0haconf
        echo "NETMASK=$netmaskvar" >> $eth0haconf
        echo "BROADCAST=$broadcast" >> $eth0haconf
        echo "IPADDR=$haipvar" >> $eth0haconf
        echo "NETWORK=$network" >> $eth0haconf
        echo "ONBOOT=no" >> $eth0haconf
        echo "ONPARENT=no" >> $eth0haconf

        if grep -q "0103" /etc/voiper_pn; then

            echo "voiper-gw01 $haipvar/$prefix/eth0:0/$broadcast cluster MailTo::root@localhost" > $haresourceconf

        else
        
                echo "voiper-sip01 $haipvar/$prefix/eth0:0/$broadcast portmap nfs cluster MailTo::root@localhost" > $haresourceconf

        fi

    fi

#

    echo "NETWORKING=yes" > $networkconf
    echo "HOSTNAME=$hostnamevar" >> $networkconf

fi

#

echo "127.0.0.1	$hostnamevar	localhost.localdomain	localhost" >> $hostsconf
        
exit 0
