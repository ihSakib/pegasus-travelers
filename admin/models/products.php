<?php
// Include database connection
include "../../db/config.php"; // Assuming this sets up the $pdo variable

// Query to combine data from all tables
$query = "
SELECT 
    'Tour' AS type,
    id, 
    package_name AS title, 
    details, 
    price, 
    created_at, 
    img, 
    location, 
    rating,
    '' AS country,
    '' AS requirements,
    '' AS country_from,
    '' AS destination
FROM travel_packages

UNION ALL

SELECT 
    'Visa' AS type,
    id, 
    title, 
    details, 
    price, 
    created_at, 
    img, 
    '' AS location,
    '' AS rating,
    country,
    requirements,
    '' AS country_from,
    '' AS destination
FROM visas

UNION ALL

SELECT 
    'Air ticket' AS type,
    id, 
    title, 
    details, 
    price, 
    created_at, 
    img, 
    '' AS location,
    '' AS rating,
    '' AS country,
    '' AS requirements,
    countryFrom AS country_from,
    destination
FROM flights

ORDER BY created_at DESC
";

// Execute the query
$stmt = $pdo->query($query);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($results);
?>