<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to access this page');
        }

        // Check if session has expired
        $lastActivity = session()->get('last_activity');
        $currentTime = time();
        $sessionTimeout = config('Session')->expiration;

        if ($lastActivity && ($currentTime - $lastActivity > $sessionTimeout)) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Session has expired. Please login again.');
        }

        // Update last activity time
        session()->set('last_activity', $currentTime);

        // For admin routes, check admin role
        $currentPath = $request->getUri()->getPath();
        if (strpos($currentPath, 'admin') === 0 && !session()->get('is_admin')) {
            return redirect()->to('/peserta/dashboard')->with('error', 'You do not have permission to access this page');
        }

        // Regenerate session ID periodically for security
        if ($lastActivity && ($currentTime - $lastActivity > config('Session')->timeToUpdate)) {
            session()->regenerate(true);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 