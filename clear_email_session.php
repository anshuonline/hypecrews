<?php
session_start();

// Clear email session variables
unset($_SESSION['send_selection_email']);
unset($_SESSION['selected_candidate_name']);
unset($_SESSION['selected_candidate_email']);
unset($_SESSION['send_deselection_email']);
unset($_SESSION['deselected_candidate_name']);
unset($_SESSION['deselected_candidate_email']);
unset($_SESSION['debug_email_info']);

// Return success response
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Session variables cleared']);