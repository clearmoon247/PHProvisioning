<html>
<body>
<?php 

//defining system names and configuration
$hosts=$_POST["hosts"];
//formats host entries to create vbs arrray
$host_entries=str_replace("\r",",",$hosts);
$host_entries=str_replace("\n","",$host_entries);

$config=$_POST["config"];
//takes configuration and converts into array
$matches=explode("\n",$config);

/* END OF VARIABLE DEFINITIIONS
PASTE CONFIGURATION BELOW with <pre></pre> tags for best formatting

Additionally, add extra line break if variable is printed at end of line. */
?>

<pre><?php
print "
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
!VBS SCRIPT: Push config to multiple devices
!
!To Use: Save the config listed below as a .vbs file
!        In SecureCRT, Click 'SCRIPT' -> Run
!        Select the .vbs script you created
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
#\$language = \"VBScript\"
#\$interface = \"1.0\"

crt.Screen.Synchronous = True

Sub Main
        HOSTS = \""; print "$host_entries"; print "\"
        HOST_ARRAY = SPLIT(HOSTS,\",\")
        USER = crt.Dialog.Prompt(\"What is the username?\")
        PASSWORD = crt.Dialog.Prompt(\"What is the user password?\", \"Login\", \"\", True)
        ENABLE =  crt.Dialog.Prompt(\"What is the enable password?\", \"Login\", \"\", True)
                For each DEVICE in HOST_ARRAY
                crt.Screen.Send \"ssh \" & USER & \"@\" & DEVICE & chr(13)
                crt.Screen.WaitForString \"assword: \"
                crt.Screen.Send PASSWORD & chr(13)
                crt.Screen.WaitForString \">\"
                crt.Screen.Send \"en\" & chr(13)
                crt.Screen.WaitForString \"assword: \"
                crt.Screen.Send ENABLE & chr(13)
                crt.Screen.WaitForString \"#\"
                crt.Screen.Send \"conf t\" & chr(13)
                crt.Screen.WaitForString \")#\"
";
foreach ($matches as $line) {
$line2=str_replace("\r","",$line);

print "                crt.Screen.Send \"$line2\" & chr(13)
                crt.Screen.WaitForString \")#\"
";}
print "                crt.Screen.Send \"end\" & chr(14)
                crt.Screen.WaitForString \"#\"
                crt.Screen.Send \"wr\" & chr(13)
                crt.Screen.WaitForString \"#\"
                crt.Screen.Send \"exit\" & chr(13)
                crt.Screen.WaitForString \"$\"
        next
End Sub

";
?></pre>
</body>
</html>
