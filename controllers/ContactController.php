<?php
/**
 * Contact Controller
 * Handles contact page
 */

class ContactController {
    /**
     * Display contact page
     */
    public function index() {
        // Load view
        require_once VIEW_PATH . '/contact/index.php';
    }
}
