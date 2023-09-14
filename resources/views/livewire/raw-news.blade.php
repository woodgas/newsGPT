<div>

    @dd($stack);


{{--        <div wire:init="showMessage"--}}
{{--             class="flex justify-center items-center">--}}
{{--            <div wire:loading>--}}
{{--                    <div role="status">--}}
{{--                        <svg aria-hidden="true" class="inline w-10 h-10 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-cyan-700" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>--}}
{{--                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>--}}
{{--                        </svg>--}}
{{--                        <span class="sr-only">Loading...</span>--}}
{{--                    </div>--}}
{{--            </div>--}}
{{--            <div wire:loading.remove>--}}
{{--                <!-- show component content here -->--}}
{{--                {{ $message }}--}}
{{--            </div>--}}
{{--        </div>--}}

    <div class="bg-gray-100 flex justify-center items-center h-screen">
        <div class="max-w-xl mx-auto p-4 bg-gray-100 rounded-lg shadow-md">
            <div class="flex flex-col space-y-10">
                @foreach($blocks as $index => $block)
                    <div class="max-w-3xl {{ $index % 2 == 0 ? 'bg-gray-200' : 'bg-gray-200' }} p-4 rounded-lg shadow-md">
                        <p class="text-base font-medium">{{ $block['title'] }}</p>
                        <button class="mt-2 py-2 px-4 bg-cyan-700 text-white rounded hover:bg-cyan-800 transition duration-200"
                                wire:click="toggleMessage({{ $index }})">
                            Show
                        </button>
                        @if($block['show_message'])
                            <div class="fixed inset-0 bg-gray-700 bg-opacity-50 flex justify-center items-center">
                                <div class="max-w-3xl bg-white p-8 rounded-lg shadow-md relative">
                                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-500"
                                            wire:click="toggleMessage({{ $index }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <p class="text-sm">{{ $block['message'] }}</p>
                                    <div class="flex justify-center items-center">
                                        <button autofocus class="absolute bottom-2 right-2 py-2 px-4 bg-cyan-700 text-white rounded hover:bg-cyan-800 transition duration-200"
                                                wire:click="toggleMessage({{ $index }})">
                                            Close</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
