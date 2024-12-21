<!-- add in models -->
<html>

  <head>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
      }

      .email-container {
        max-width: 600px;
        margin: auto;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }

      .header {
        text-align: center;
        padding: 20px 0;
        background-color: #4CAF50;
        color: white;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
      }

      .content {
        padding: 20px;
        text-align: left;
      }

      .footer {
        text-align: center;
        padding: 20px;
        font-size: 12px;
        color: #888888;
      }

      .button {
        display: block;
        width: 200px;
        margin: 20px auto;
        padding: 10px;
        text-align: center;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
      }

      .button:hover {
        background-color: #45a049;
      }
    </style>
  </head>

  <body>
    <div class='email-container'>
      <div class='header'>
        <h1>Pegasus Travelers</h1>
      </div>
      <div class='content'>
        <p>Hello, $userName,</p>
        <p>We received a request to reset your password. Click the button below to reset it:</p>
        <a href='$resetLink' class='button'>Reset Your Password</a>
        <p>If you didn't request a password reset, please ignore this email.</p>
        <p>Thank you,<br>The Pegasus Travelers Team</p>
      </div>
      <div class='footer'>
        &copy; year Pegasus Travelers. All rights reserved.
      </div>
    </div>
  </body>

</html>