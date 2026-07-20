<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Fetch standard notifications via AJAX
     * GET /notifications/fetch
     */
    public function fetch()
    {
        $userId = session()->get('userId');
        
        if (!$userId) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $limit = $this->request->getGet('limit') ?? 10;
        $notifications = $this->notificationModel->getRecent($userId, $limit);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'unread_count'  => $unreadCount,
                'notifications' => $notifications
            ]
        ]);
    }

    /**
     * Mark all notifications as read
     * POST /notifications/read-all
     */
    public function readAll()
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $this->notificationModel->where('user_id', $userId)
                                ->where('is_read', 0)
                                ->set(['is_read' => 1])
                                ->update();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Mark single notification as read
     * POST /notifications/(:num)/read
     */
    public function read($id)
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $notif = $this->notificationModel->find($id);
        
        // Ensure notification belongs to the logged in user
        if ($notif && $notif['user_id'] == $userId && $notif['is_read'] == 0) {
            $this->notificationModel->update($id, ['is_read' => 1]);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Notification marked as read'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error', 
            'message' => 'Notification not found or access denied'
        ]);
    }
}
