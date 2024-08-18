<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Notif;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Str;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = Notif::find($id);
        if ($notification) {
            $notification->is_read  = 1;
            $notification->save();
        }
        return redirect()->back();
    }

    public function delete($id)
    {
        $notification = Notif::find($id);
        if ($notification) {
            $notification->delete();
        }
        return redirect()->back();
    }
}
