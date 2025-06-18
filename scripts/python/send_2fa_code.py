import smtplib
import random
import ssl
import sys
import os
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime, timedelta
import json
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

recipient_email = sys.argv[1]
username = sys.argv[2]

# Generate the code
code = str(random.randint(100000, 999999))
expiry = (datetime.now() + timedelta(minutes=5)).timestamp()

# Email credentials from environment variables
sender_email = os.getenv("SMTP_EMAIL")
sender_password = os.getenv("SMTP_PASSWORD")
if not sender_email or not sender_password:
    print(json.dumps({"status": "error", "message": "SMTP_EMAIL or SMTP_PASSWORD not set in .env file"}))
    sys.exit(1)

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