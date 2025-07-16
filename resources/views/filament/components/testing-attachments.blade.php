<div class="grid grid-cols-2 md:grid-cols-3 gap-4">
    @forelse($attachments as $media)
        <div class="border rounded shadow-sm overflow-hidden">
            <a href="{{ $media->getFullUrl() }}" target="_blank" title="Klik untuk buka di tab baru">
                <img src="{{ $media->getFullUrl() }}" alt="Uploaded image" class="w-full h-auto hover:opacity-75 transition">
            </a>
            <div class="text-xs text-center p-1 truncate">{{ $media->name }}</div>
        </div>
    @empty
        <p class="text-sm text-gray-500">Belum ada gambar diunggah.</p>
    @endforelse
</div>