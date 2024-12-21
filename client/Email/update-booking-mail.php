<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status Update</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
      }

      .email-container {
        max-width: 600px;
        margin: 20px auto;
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

      .status-badge {
        display: inline-block;
        padding: 5px 10px;
        color: white;
        border-radius: 5px;
        background-color:
          <?= $orderStatus == 'pending' ? '#FFC107' : ($orderStatus == 'completed' ? '#28A745' : '#DC3545') ?>
        ;
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
    <div class="email-container">
      <div class="header">
        <h1>Pegasus Travelers</h1>
      </div>
      <div class="content">
        <p>Dear <?= htmlspecialchars($userName) ?>,</p>
        <p>We are pleased to inform you that your booking status has been updated. Below are your booking details:</p>
        <p><strong>Booking ID:</strong> <?= htmlspecialchars($orderId) ?></p>
        <p><strong>Package:</strong> <?= htmlspecialchars($packageName) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($category) ?></p>
        <p><strong>Status:</strong>
          <span class="status-badge"><?= htmlspecialchars($orderStatus) ?></span>
        </p>
        <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
        <p>Thank you for choosing Pegasus Travelers.</p>
        <p>Best regards,<br>The Pegasus Travelers Team</p>
      </div>
      <div class="footer">
        &copy; <?= date('Y') ?> Pegasus Travelers. All rights reserved.
      </div>
    </div>
  </body>

</html>