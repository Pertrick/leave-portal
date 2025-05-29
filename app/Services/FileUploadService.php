<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Allowed file types
     */
    private const ALLOWED_MIME_TYPES = [
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        // Images
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/gif'
    ];

    /**
     * Upload a file to the specified disk and path
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function upload(UploadedFile $file, string $path = 'uploads', string $disk = 'public'): string
    {
        // Generate a unique filename
        $filename = $this->generateUniqueFilename($file);
        
        // Store the file
        $filePath = $file->storeAs($path, $filename, $disk);
        
        if (!$filePath) {
            throw new \Exception('Failed to upload file');
        }
        
        // Return just the relative path, not the full URL
        return $filePath;
    }

    /**
     * Delete a file from storage
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        // Extract the relative path from the full URL if needed
        $relativePath = $this->getRelativePath($path);
        
        if (Storage::disk($disk)->exists($relativePath)) {
            return Storage::disk($disk)->delete($relativePath);
        }
        
        return false;
    }

    /**
     * Generate a unique filename for the uploaded file
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = Str::slug($originalName);
        
        return $sanitizedName . '-' . uniqid() . '.' . $extension;
    }

    /**
     * Get the full URL for a file
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function getUrl(string $path, string $disk = 'public'): string
    {
        // Extract the relative path from the full URL if needed
        $relativePath = $this->getRelativePath($path);
        
        if (Storage::disk($disk)->exists($relativePath)) {
            return asset('storage/' . $relativePath);
        }
        
        return '';
    }

    /**
     * Get the relative path from a full URL or path
     *
     * @param string $path
     * @return string
     */
    private function getRelativePath(string $path): string
    {
        // If the path is a full URL, extract the relative path
        if (Str::startsWith($path, ['http://', 'https://'])) {
            $path = parse_url($path, PHP_URL_PATH);
            // Remove 'storage/' from the beginning if it exists
            $path = Str::after($path, 'storage/');
        }
        
        return $path;
    }

    /**
     * Validate file size and type
     *
     * @param UploadedFile $file
     * @param array $allowedTypes
     * @param int $maxSize
     * @return bool
     */
    public function validate(UploadedFile $file, array $allowedTypes = [], int $maxSize = 5120): bool
    {
        // Check file size (default 5MB)
        if ($file->getSize() > ($maxSize * 1024)) {
            return false;
        }

        // Use default allowed types if none specified
        $typesToCheck = !empty($allowedTypes) ? $allowedTypes : self::ALLOWED_MIME_TYPES;

        // Check file type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $typesToCheck)) {
            return false;
        }

        return true;
    }

    /**
     * Get allowed file extensions
     *
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        return [
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
            'txt',
            'jpg',
            'jpeg',
            'png',
            'gif'
        ];
    }
} 