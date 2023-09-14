<button {{ $attributes->merge([
    "class" => "py-2 px-4 bg-cyan-700 text-white rounded transition duration-200"]) }}>
    {{ $slot }}
</button>
