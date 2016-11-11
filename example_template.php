<html>
<body>
<?php 

//defining system name and passwords
$hostname=$_POST["hostname"];
$enable_secret=$_POST["enable_secret"];
$admin_name=$_POST["admin_name"];
$admin_secret=$_POST["admin_secret"];

//Provisioning Workstation IP Information
$workstation_network=$_POST["workstation_subnet_id"];
$workstation_mask=$_POST["workstation_subnet_mask"];
$workstation_wildcard=wildcard("$workstation_mask");
$workstation_gateway=$_POST["workstation_subnet_id"];
$workstation_end=$_POST["workstation_subnet_id"];

//Defines First Usable IP
++$workstation_gateway;
//Defines the Fifth Usable IP
++$workstation_end; ++$workstation_end; ++$workstation_end; ++$workstation_end; ++$workstation_end;

$voice_network=$_POST["voice_subnet_id"];
$voice_mask=$_POST["voice_subnet_mask"];
$voice_wildcard=wildcard("$voice_mask");
$voice_gateway=$_POST["voice_subnet_id"];
$voice_end=$_POST["voice_subnet_id"];

//Defines First Usable IP
++$voice_gateway;
//Defines the Fifth Usable IP
++$voice_end; ++$voice_end; ++$voice_end; ++$voice_end; ++$voice_end;

$management_network=$_POST["management_subnet_id"];
$management_mask=$_POST["management_subnet_mask"];
$management_wildcard=wildcard("$management_mask");
$management_gateway=$_POST["management_subnet_id"];
$management_end=$_POST["management_subnet_id"];

//Defines First Usable IP
++$management_gateway;
//Defines the Fifth Usable IP
++$management_end; ++$management_end; ++$management_end; ++$management_end; ++$management_end;

$guest_network=$_POST["guest_subnet_id"];
$guest_mask=$_POST["guest_subnet_mask"];
$guest_wildcard=wildcard("$guest_mask");
$guest_gateway=$_POST["guest_subnet_id"];
$guest_end=$_POST["guest_subnet_id"];

//Defines First Usable IP
++$guest_gateway;
//Defines the Fifth Usable IP
++$guest_end; ++$guest_end; ++$guest_end; ++$guest_end; ++$guest_end;

//Provisioning Broadband
$broadband_ip=$_POST["broadband_ip"];
$broadband_mask=$_POST["broadband_mask"];
$broadband_gateway=$_POST["broadband_gateway"];

//Provisioning BGP & WAN
$local_as=$_POST["local_as"];
$wan_peer_as=$_POST["wan_peer_as"];
$wan_ip=$_POST["wan_ip"];
$wan_peer=$_POST["wan_peer"];
$wan_mask=$_POST["wan_mask"];


//Function to convert subnet mask to wildcard mask.
//Current function works from /16-/30
function wildcard($subnet_mask)
{
if($subnet_mask =='255.255.255.252') $wildcard_mask='0.0.0.3';
elseif($subnet_mask =='255.255.255.248') $wildcard_mask='0.0.0.7';
elseif($subnet_mask =='255.255.255.240') $wildcard_mask='0.0.0.15';
elseif($subnet_mask =='255.255.255.224') $wildcard_mask='0.0.0.31';
elseif($subnet_mask =='255.255.255.192') $wildcard_mask='0.0.0.63';
elseif($subnet_mask =='255.255.255.128') $wildcard_mask='0.0.0.127';
elseif($subnet_mask =='255.255.255.0') $wildcard_mask='0.0.0.255';
elseif($subnet_mask =='255.255.254.0') $wildcard_mask='0.0.1.255';
elseif($subnet_mask =='255.255.252.0') $wildcard_mask='0.0.3.255';
elseif($subnet_mask =='255.255.248.0') $wildcard_mask='0.0.7.255';
elseif($subnet_mask =='255.255.240.0') $wildcard_mask='0.0.15.255';
elseif($subnet_mask =='255.255.224.0') $wildcard_mask='0.0.31.255';
elseif($subnet_mask =='255.255.192.0') $wildcard_mask='0.0.63.255';
elseif($subnet_mask =='255.255.128.0') $wildcard_mask='0.0.127.255';
elseif($subnet_mask =='255.255.0.0') $wildcard_mask='0.0.255.255';
else $wildcard_mask='ERROR';
return $wildcard_mask;
}

/* END OF VARIABLE DEFINITIIONS
PASTE CONFIGURATION BELOW with <pre></pre> tags for best formatting

Additionally, add extra line break if variable is printed at end of line. */
?>
<pre>
<?php
print "$hostname Configuration
!";
print "
service timestamps log datetime msec
service timestamps debug datetime msec
service password-encryption
!
hostname $hostname
!
username $admin_name priv 15 secret $admin_secret
!
ip dhcp excluded-address $workstation_gateway $workstation_end
ip dhcp excluded-address $voice_gateway $voice_end
ip dhcp excluded-address $guest_gateway $guest_end
!
ip dhcp pool WORKSTATION
 network $workstation_network $workstation_mask
 domain-name HOME.LOCAL
 dns-server $workstation_gateway
 default-router $workstation_gateway
!
ip dhcp pool VOICE
 network $voice_network $voice_mask
 domain-name HOME.LOCAL
 dns-server $voice_gateway
 default-router $voice_gateway
!
ip dhcp pool GUEST
 network $guest_network $guest_mask
 domain-name HOME.LOCAL
 dns-server $guest_gateway
 default-router $guest_gateway
!
no ip domain-lookup
!
ip access-list extended WORKSTATION-OUT-ACL
 permit ip $voice_network $voice_wildcard $workstation_network $workstation_wildcard
 deny ip $guest_network $guest_wildcard any log
 permit ip any any
!
ip access-list extended NAT_LIST
 permit ip $workstation_network $workstation_wildcard any
 permit ip $guest_network $guest_wildcard any
!
interface GigabitEthernet0/0
no shutdown
no ip address
duplex auto
speed auto
!
interface GigabitEthernet0/0.45
 description Workstation Network
 encapsulation dot1Q 45
 ip address $workstation_gateway $workstation_mask
 ip access-group WORKSTATION-OUT-ACL out
 ip nat inside
!
interface GigabitEthernet0/0.46
 description Management Network
 encapsulation dot1Q 46
 ip address $management_gateway $management_mask
!
interface GigabitEthernet0/0.47
 description Voice Network
 encapsulation dot1Q 47
 ip address $voice_gateway $voice_mask
!
interface GigabitEthernet0/0.101
 description Guest Network
 encapsulation dot1Q 101
 ip address $guest_gateway $guest_mask
 ip nat inside
!
interface GigabitEthernet0/1
 description Broadband Interface
 ip address $broadband_ip $broadband_mask
 duplex auto
 speed auto
 no shutdown
 ip nat outside
!
interface GigabitEthernet0/2
 description NOT IN USE
 no ip address
 shutdown
!
ip route 0.0.0.0 0.0.0.0 $broadband_gateway 99
!
ip nat inside source list NAT_LIST interface GigabitEthernet0/1 overload
";

//If statement to verify WAN Interface & BGP Configuration
if($wan_peer > '0') {print "
interface Serial0/0/0
 description WAN Interface
 ip address $wan_ip $wan_mask
 no shutdown
!
ip route 0.0.0.0 0.0.0.0 $wan_peer 100

router bgp $local_as
 bgp router-id $wan_ip
 redistribute connected
 neighbor $wan_peer remote-as $wan_peer_as
 neighbor $wan_peer description WAN Circuit
 neighbor $wan_peer ebgp-multihop 3
 neighbor $wan_peer update-source $wan_ip
 neighbor $wan_peer default-originate
 neighbor $wan_peer soft-reconfiguration inbound
 !";}
else { 
print "!";}
print "
line con 0
!
line aux 0
!
line vty 0 4
 login
!
end
";?>
</pre>

</body>
</html>
