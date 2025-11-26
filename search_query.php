<?php
// Include the database connection file.
// Make sure this file exists and contains your database connection details.
include 'assets/db/db.php';

if (isset($_POST['keyword'])) {
    $searchKeyword = trim($_POST['keyword']);
    
    // Check if the keyword is not empty after trimming
    if (empty($searchKeyword)) {
        echo '<div class="alert alert-danger">No search keyword provided.</div>';
        exit;
    }
    
    // Split the keyword into individual words, converting to lowercase for better matching.
    $words = explode(' ', strtolower($searchKeyword));
    
    // Build the dynamic WHERE clause
    $whereConditions = [];
    $bindParams = '';
    $bindValues = [];

    // Loop through each word to create a set of LIKE conditions
    foreach ($words as $word) {
        // Skip empty words that might result from multiple spaces
        if (!empty($word)) {
            $wordWildcard = "%{$word}%";
            
            // Add a group of OR conditions for each word
            $whereConditions[] = "(LOWER(tblresearches.title) LIKE ? OR LOWER(tblaccount.acc_name) LIKE ? OR LOWER(tblresearches.authors) LIKE ?)";
            
            // Collect the bind parameters and values for each condition
            $bindParams .= 'sss';
            $bindValues[] = $wordWildcard;
            $bindValues[] = $wordWildcard;
            $bindValues[] = $wordWildcard;
        }
    }

    // Join the conditions with AND to ensure all words are present
    // This is the key to the "out-of-order" multi-word search.
    $whereClause = implode(' AND ', $whereConditions);

    // Prepare the full SQL statement with the dynamic WHERE clause
    $stmt = $conn->prepare("
        SELECT * FROM tblresearches
        INNER JOIN tblacademic_year ON tblacademic_year.ayid = tblresearches.ayid
        INNER JOIN tblmanuscript_type ON tblmanuscript_type.manus_typeid = tblresearches.typeid
        LEFT JOIN tbladviser ON tbladviser.adviser_paperid = tblresearches.titleid
        LEFT JOIN tblaccount ON tblaccount.accountid = tbladviser.advise_accid
        WHERE " . $whereClause . "
        LIMIT 50
    ");

    // Check if the prepare statement was successful
    if ($stmt === false) {
        echo '<div class="alert alert-danger">Error preparing statement: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }

    // Bind the parameters dynamically
    // The `...` (splat) operator is a modern way to pass an array of values to bind_param.
    $stmt->bind_param($bindParams, ...$bindValues);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '
                <div class="result-card p-3 mb-3 border rounded bg-white shadow-sm">
                    <div class="fw-bold text-primary">'.htmlspecialchars($row['title']).'</div>
                    <div class="text-muted small">
                        Type: '.htmlspecialchars($row['manus_type_desc']).' |
                        A.Y.: '.htmlspecialchars($row['ay_from']).' |
                        Adviser: '.htmlspecialchars($row['acc_name'] ?? 'N/A').'
                    </div>
                    <div class="text-muted small">
                        Researchers: '.htmlspecialchars($row['authors']).'
                    </div>
                </div>
            ';
        }
    } else {
        echo '<div class="alert alert-warning">No matching records found.</div>';
    }

    $stmt->close();
} else {
    echo '<div class="alert alert-danger">No search keyword provided.</div>';
}
?>