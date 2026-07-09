<?php
/**
 * Shared data-fetch helper for the 3 FAAS Building PDF generators.
 * Not an endpoint itself - included by generate_*_pdf.php files.
 * Requires $conn (mysqli) to already be available via db.php.
 */

function faas_pdf_fetch_record($conn, $buildingId) {
    $stmt = $conn->prepare(
        "SELECT p.*, b.*
         FROM tbl_faas_building b
         INNER JOIN tbl_properties p ON p.id = b.property_id
         WHERE b.id = ?"
    );
    $stmt->bind_param('i', $buildingId);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$record) {
        return null;
    }

    $stmt = $conn->prepare("SELECT * FROM tbl_faas_building_items WHERE faas_building_id = ?");
    $stmt->bind_param('i', $buildingId);
    $stmt->execute();
    $record['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $stmt = $conn->prepare("SELECT * FROM tbl_faas_building_superseded WHERE faas_building_id = ? ORDER BY id DESC");
    $stmt->bind_param('i', $buildingId);
    $stmt->execute();
    $record['superseded'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $record;
}

/** Marks a building record as just generated (used by all 3 generators). */
function faas_pdf_mark_generated($conn, $buildingId) {
    $stmt = $conn->prepare("UPDATE tbl_faas_building SET last_generated_at = NOW() WHERE id = ?");
    $stmt->bind_param('i', $buildingId);
    $stmt->execute();
    $stmt->close();
}

/** Basic peso amount-to-words converter, used on the Tax Declaration. */
function faas_number_to_words($number) {
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
             'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
             'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    $number = (int) round($number);
    if ($number === 0) return 'Zero Pesos Only';

    $convertGroup = function ($n) use ($ones, $tens, &$convertGroup) {
        if ($n < 20) return $ones[$n];
        if ($n < 100) return trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]);
        return trim($ones[intdiv($n, 100)] . ' Hundred ' . $convertGroup($n % 100));
    };

    $groups = ['', ' Thousand', ' Million', ' Billion'];
    $words = '';
    $i = 0;
    while ($number > 0) {
        $chunk = $number % 1000;
        if ($chunk !== 0) {
            $words = $convertGroup($chunk) . $groups[$i] . ' ' . $words;
        }
        $number = intdiv($number, 1000);
        $i++;
    }

    return trim($words) . ' Pesos Only';
}