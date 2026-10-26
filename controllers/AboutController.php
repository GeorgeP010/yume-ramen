<?php
/**
 * About Controller
 * Handles about page
 */

class AboutController {
    /**
     * Display about page
     */
    public function index() {
        // Load view
        require_once VIEW_PATH . '/about/index.php';
    }
}
