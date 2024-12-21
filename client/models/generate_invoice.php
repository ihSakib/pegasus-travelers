<?php

require_once('../../dompdf/autoload.inc.php'); // Adjust the path to where you placed Dompdf



use Dompdf\Dompdf;



// Include your database connection

require_once('../../db/config.php');



// Get the order_id from GET request

$order_id = $_GET['order_id'];



// Fetch order details

$query = $pdo->prepare("

    SELECT orders.id, orders.status, orders.total_amount AS total_price, 

           oi.package_name, oi.category, oi.price AS item_price, users.name, users.email, users.location, users.phone 

    FROM orders

    JOIN order_items oi ON orders.id = oi.order_id

    JOIN users ON orders.customer_id = users.id

    WHERE orders.id = :order_id

");



$query->execute(['order_id' => $order_id]);

$order_items = $query->fetchAll(PDO::FETCH_ASSOC);







// Calculate the discount

$discount = (($order_items[0]['item_price'] - $order_items[0]['total_price']) / $order_items[0]['item_price']) * 100;





// Create an instance of Dompdf

$dompdf = new Dompdf();



// Generate HTML content with data from the database

$html = '

<!DOCTYPE html>

<html>

<head>

    <style>

        body {

            font-family: "Helvetica Neue", Arial, sans-serif;

            margin: 0;

            padding: 0;

            background: #f4f4f4;

            border-radius: 8px;

        }

        .container {

            width: 80%;

            margin: 60px auto;

            padding: 20px;

            background: #ffffff;

            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

        }

        .header {

            display: table;

            width: 100%;

            margin-bottom: 20px;

            border-bottom: 2px solid #eeeeee;

            padding-bottom: 20px;

        }

        .header .logo, .header .booking-details {

            display: table-cell;

            vertical-align: middle;

        }

        .header .logo {

            width: 50%;

            text-align: left;

        }

        .header .booking-details {

            width: 50%;

            text-align: right;

        }

        .header img {

            display:block;

            margin-left:10px;

            width: 70px; /* Adjust size as needed */

        }

        .header h1 {

            font-size: 24px;

            margin: 10px 0;

            color: #333333;

        }

        .header p {

            margin: 5px 0;

            font-size: 14px;

        }

        .header .status {

            font-weight: bold;

            color: #555555;

        }

        .company-info, .customer-info {

            background: #f9f9f9;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

        }

        .company-info h2, .customer-info h2 {

            margin-top: 0;

            color: #333333;

            font-size: 18px;

        }

        .company-info p, .customer-info p {

            margin: 5px 0;

            color: #555555;

            font-size: 14px;

        }

        table {

            width: 100%;

            border-collapse: collapse;

            margin-bottom: 20px;

        }

        th, td {

            padding: 12px;

            border: 1px solid #dddddd;

            text-align: left;

            font-size: 14px;

        }

        th {

            background-color: #f4f4f4;

            color: #333333;

        }

        td {

            background-color: #ffffff;

        }

        .total {

            text-align: right;

            font-weight: bold;

            margin-bottom: 20px;

            font-size: 16px;

        }

            

       .discount {

            text-align: right;

            font-size: 14px;

            color: #fd7e14; /* Bootstrap orange */

        }       



        .footer {

            text-align: center;

            font-style: italic;

            color: #777777;

            font-size: 14px;

            margin-top:40px;

        }

        .watermark {

            position: fixed;

            top: 0px;

            right: 0px;

            padding: 10px;

            border-radius: 5px;

            color: #ffffff;

            font-size: 16px;

            font-weight: bold;

            text-transform: uppercase;

        }

        .watermark.pending {

            background-color: rgba(255, 0, 0, 0.7);

        }

        .watermark.completed {

            background-color: rgba(0, 128, 0, 0.7);

        }

        .watermark.cancelled {

            background-color: rgba(255, 165, 0, 0.7);

        }

    </style>

</head>

<body>

    <div class="container">

        <div class="header">

            <div class="logo">

                <img src="https://pegasustravelers.com/img/logo.png" alt="Logo"/>

            </div>

            <div class="booking-details">

                <h1>Invoice</h1>

                <p>Booking ID: ' . htmlspecialchars($order_items[0]['id']) . '</p>

                <p>Date: ' . date('Y-m-d') . '</p>

                <p class="status">Status: ' . htmlspecialchars(ucfirst($order_items[0]['status'])) . '</p>

            </div>

        </div>';



if ($order_items[0]['status'] == 'pending') {

    $html .= '<div class="watermark pending">Pending - Unpaid</div>';

} elseif ($order_items[0]['status'] == 'completed') {

    $html .= '<div class="watermark completed">Paid</div>';

} elseif ($order_items[0]['status'] == 'canceled') {

    $html .= '<div class="watermark cancelled">Eligible for refund if paid</div>';

}



$html .= '

        <div class="company-info">

            <h2>Company Information</h2>

            <p>Pegasus Travelers</p>

            <p>Email: mail@pegasustravelers.com</p>

            <p>Phone: +8801864946718</p>

            <p>Address: Flat: 3/A, TA 199, Baluchar Road, South Badda, Dhaka 1212</p>

        </div>



        <div class="customer-info">

            <h2>Customer Information</h2>

            <p>Name: ' . htmlspecialchars($order_items[0]['name']) . '</p>

            <p>Email: ' . htmlspecialchars($order_items[0]['email']) . '</p>

            <p>Address: ' . htmlspecialchars($order_items[0]['location']) . '</p>

            <p>Phone: ' . htmlspecialchars($order_items[0]['phone']) . '</p>

        </div>



        <h2>Booking Details</h2>

        <table>

            <tr>

                <th>Category</th>

                <th>Package</th>

                <th>Price</th>

            </tr>';



foreach ($order_items as $item) {

    $html .= '

            <tr>

                <td>' . htmlspecialchars($item['category']) . '</td>

                <td>' . htmlspecialchars($item['package_name']) . '</td>

                <td>' . number_format($item['item_price'], 2) . ' BDT.' . '</td>

            </tr>';

}



$html .= '</table>';



// Show discount if applicable

if (true) {

    $html .= '<p class="discount">Discount: ' . $discount . "%" . '</p>';

}



$html .= '

        <p class="total">Total: ' . number_format($order_items[0]['total_price'], 2) . ' BDT.' . '</p>



        <div class="footer">

            <p>Thank you for choosing Pegasus Travelers!</p>

        </div>

    </div>

</body>

</html>';



// Load HTML content into Dompdf

$dompdf->loadHtml($html);

$dompdf->set_option('isRemoteEnabled', true);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();



// Output the PDF as a download

$dompdf->stream('invoice-' . $order_id . '.pdf');

?>