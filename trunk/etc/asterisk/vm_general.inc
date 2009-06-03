
format = wav
serveremail = Voiper PBX
attach = yes
maxmessage = 180
minmessage = 3
skipms = 3000
maxsilence = 5
silencethreshold = 128
maxlogins = 3
pbxskip = yes
fromstring = Voiper Voicemail System
sendvoicemail = yes
review = yes
operator = no
maxmsg = 9999
maxgreet = 60
externnotify = 


externpass = 


emailsubject = [Voiper PBX]: Hai ${VM_MSGNUM} nuovi messaggi nella casella vocale ${VM_MAILBOX}
emailbody = Salve ${VM_NAME}.\n\n\tHai ricevuto un messaggio Vocale della durata di ${VM_DUR}, messaggio: (numero ${VM_MSGNUM}) nella mailbox ${VM_MAILBOX} da ${VM_CALLERID}\nin data ${VM_DATE}.\nGrazie!\n\n\t\t\t\tVoiper PBX\n

