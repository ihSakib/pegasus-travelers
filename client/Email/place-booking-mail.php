<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
      }

      .email-container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
      }

      .header {
        text-align: center;
        padding: 30px 0;
        background-color: #4caf50;
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
      }

      .content {
        padding: 20px 30px;
        text-align: left;
      }

      .footer {
        text-align: center;
        padding: 15px;
        font-size: 12px;
        color: #888888;
        border-top: 1px solid #eaeaea;
      }

      .button {
        display: block;
        width: fit-content;
        margin: 20px auto;
        padding: 12px 20px;
        text-align: center;
        background-color: #4caf50;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
      }

      .button:hover {
        background-color: #45a049;
      }

      .status {
        padding: 10px 20px;
        background-color: #f0f0f0;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
        color: #333333;
        margin-top: 10px;
      }

      @media only screen and (max-width: 600px) {
        .content {
          padding: 15px;
        }
      }
    </style>
  </head>

  <body>
    <div class='email-container'>
      <div class='header'>
        <h1>Pegasus Travelers</h1>
      </div>
      <div class='content'>
        <p>Dear <?= htmlspecialchars($userName) ?>,</p>
        <p>Thank you for your booking with Pegasus Travelers! Here are your booking details:</p>
        <p><strong>Booking ID:</strong> <?= $order_id ?></p>
        <p><strong>Package:</strong> <?= htmlspecialchars($title) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($category) ?></p>
        <p><strong>Quantity:</strong> <?= $quantity ?></p>
        <p><strong>Total Amount:</strong> $<?= number_format($total_amount, 2) ?></p>
        <div class="status">
          <strong>Status:</strong> <?= htmlspecialchars($status) ?>
        </div>
        <p>Our team will call you soon.</p>
        <p>Thank you,<br>The Pegasus Travelers Team</p>
      </div>
      <div class='footer'>
        &copy; <?= date('Y') ?> Pegasus Travelers. All rights reserved.
      </div>
    </div>
  </body>

</html>