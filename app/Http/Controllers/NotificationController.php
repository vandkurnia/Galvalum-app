<?php

namespace App\Http\Controllers;

use App\Models\CustomNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $notifications = CustomNotification::latest()->get();


        foreach ($notifications as $notification) {
            $notification->url = route('notifications.read', ['id' => $notification->id_notifikasi]);
            // switch ($notification->type) {
            //     case 'piutang':
            //         $notification->url = route('laporan.piutang');
            //         break;
            //     case 'hutang':
            //         $notification->url = route('laporan.hutang');
            //         break;
            //     default:
            //         $notification->url = "#";
            //         break;
            // }
        }

        return response()->json($notifications);
    }
    public function markAsRead($id)
    {
        $notification = CustomNotification::find($id);

        if ($notification) {
            $notification->read_at = Carbon::now();
            $notification->save();

            if ($notification->type == 'piutang') {

                // Mengambil parameter yang sudah ada dari URL saat ini
                // $queryParams = request()->query();

                // Menambahkan solve dengan nilai $notification->id_notification
                // $queryParams['solve'] = $notification->id_notification;
                // return redirect()->route('laporan.piutang', ['solve' => $notification->id_notification]);

                
                return redirect()->to('laporan/piutang?solve=' . $notification->id_data);
            } elseif ($notification->type == 'hutang') {
                return redirect()->route('laporan.hutang');
            }

            // If type is neither 'piutang' nor 'hutang', redirect back or handle as needed
            return Redirect::back();
        } else {
            return Redirect::back();
        }
    }

    public function delete($id)
    {
        $notification = CustomNotification::find($id);

        if ($notification) {

            $notification->delete();
            return response()->json([
                'message' => 'Berhasil menghapus notifikasi.'
            ]);
        } else {

            return response()->json([
                'message' => 'Gagal menghapus notifikasi.'
            ]);
            // return redirect()->back()->with('error', 'Gagal menghapus notifikasi.');
        }
    }
}
