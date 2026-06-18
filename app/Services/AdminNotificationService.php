<?php

namespace App\Services;

class AdminNotificationService
{
    protected $path;

    public function __construct()
    {
        $this->path = storage_path('app/admin_notifications.json');
        if (!file_exists($this->path)) {
            file_put_contents($this->path, json_encode([]));
        }
    }

    protected function readAll()
    {
        $raw = @file_get_contents($this->path);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    protected function writeAll(array $data)
    {
        file_put_contents($this->path, json_encode(array_values($data), JSON_PRETTY_PRINT));
    }

    public function all()
    {
        return $this->readAll();
    }

    public function unreadCount()
    {
        $all = $this->readAll();
        return collect($all)->where('read', false)->count();
    }

    public function append(array $payload)
    {
        $all = $this->readAll();
        $id = uniqid();
        $payload = array_merge([
            'id' => $id,
            'created_at' => now()->toDateTimeString(),
            'read' => false,
        ], $payload);
        $all[] = $payload;
        $this->writeAll($all);
        return $id;
    }

    public function markRead(string $id)
    {
        $all = $this->readAll();
        $changed = false;
        foreach ($all as &$item) {
            if (isset($item['id']) && $item['id'] === $id) {
                $item['read'] = true;
                $item['read_at'] = now()->toDateTimeString();
                $changed = true;
                break;
            }
        }
        if ($changed) $this->writeAll($all);
        return $changed;
    }

    public function markAllRead()
    {
        $all = $this->readAll();
        $changed = false;
        foreach ($all as &$item) {
            if (empty($item['read'])) {
                $item['read'] = true;
                $item['read_at'] = now()->toDateTimeString();
                $changed = true;
            }
        }
        if ($changed) $this->writeAll($all);
        return $changed;
    }

    public function find(string $id)
    {
        $all = $this->readAll();
        foreach ($all as $item) {
            if (isset($item['id']) && $item['id'] === $id) return $item;
        }
        return null;
    }
}
