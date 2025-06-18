import smtplib
import random
import ssl
import sys
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime, timedelta
import json

recipient_email = sys.argv[1]
username = sys.argv[2]

# Generate 2FA code
code = str(random.randint(100000, 999999))
expiry = (datetime.now() + timedelta(minutes=5)).timestamp()

# Hardcoded Gmail credentials
sender_email = "dungeons.monsters@gmail.com"
sender_password = "kajc wjkc tlkj vqxv"  # Replace with your app password if 2FA is on

message = MIMEMultipart("alternative")
message["Subject"] = "Your 2FA Code"
message["From"] = sender_email
message["To"] = recipient_email

text = f"""\
Hello {username},
Your 2FA code is: {code}
This code will expire in 5 minutes."""

message.attach(MIMEText(text, "plain"))

context = ssl.create_default_context()

try:
    with smtplib.SMTP_SSL("smtp.gmail.com", 465, context=context) as server:
        server.login(sender_email, sender_password)
        server.sendmail(sender_email, recipient_email, message.as_string())
    print(json.dumps({"status": "success", "code": code, "expiry": expiry}))
except Exception as e:
    print(json.dumps({"status": "error", "message": str(e)}))