<div class="container mx-auto mt-10">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden md:max-w-xl">
        {{--        News type select--}}
        <div class="md:flex">
            <div class="w-full px-4 py-6">
                <div class="relative">
                    @error('selectedType') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    <label for="name" class="sr-only">{{ __('Select type') }}</label>
                    <select
                        {{ !empty($rssResponse) ? 'disabled' : '' }}
                        id="selectType" name="selectType"
                        wire:model="selectedType"
                        wire:loading.attr="disabled"
                        wire:disabled="isLoading"
                        class="block appearance-none w-full bg-gray-100 border border-gray-300 hover:border-gray-500
                                px-4 py-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option value="" class="bg-gray-200 pl-4">{{ __('Please select News type') }}</option>
                        @foreach(array_keys($rssTypes) as $type)
                            <option value="{{ $type }}">&nbsp;&nbsp;{{ $type }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <x-svg-arrow-down/>
                    </div>
                </div>
                @if(empty($selectedType))
                    <x-loader-line/> @endif
            </div>
        </div>
        {{--        News category select--}}
        @if(!empty($selectedType) AND !empty($rssTypes[$selectedType]))
            <div class="md:flex">
                <div class="w-full px-4 pb-4">
                    <div class="relative">
                        @error('selectedCategory') <span
                            class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        <label for="name" class="sr-only">{{ __('Select category') }}</label>
                        <select
                            {{ !empty($rssResponse) ? 'disabled' : '' }}
                            id="selectCategory" name="selectCategory"
                            wire:model="selectedCategory"
                            wire:loading.attr="disabled"
                            class="block appearance-none w-full bg-gray-100 border border-gray-300 hover:border-gray-500
                                px-4 py-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="" class="bg-gray-200 p-1">
                                {{ __('Type is ') . $selectedType .
                                   __(' - Please select category') }}
                            </option>
                            @foreach(array_keys($rssTypes[$selectedType]) as $category)
                                <option value="{{ $category }}">&nbsp;&nbsp;{{ $category }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <x-svg-arrow-down/>
                        </div>
                    </div>
                    @if(empty($rssResponse))
                        <x-loader-line/>
                    @endif
                </div>
            </div>
        @endif
        {{--        Convert RSS button--}}
        @if(!empty($selectedCategory) AND
            !empty($selectedType))
            <div class="md:flex">
                <div class="w-full px-4">
                    @if(empty($rssResponse))
                        <x-button
                            x-on:click="$wire.convert()"
                            class="hover:bg-cyan-800"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50"
                        >
                            {{ __('Convert RSS') }}
                        </x-button>
                    @else
                        <x-button
                            x-on:click=" $wire.resetAll()"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50"
                            class="hover:bg-fuchsia-900"
                        >
                            {{ __('Reset all') }}
                        </x-button>
                    @endif
                </div>
                @endif
            </div>
    </div>

    {{-- News list--}}
    @if(!empty($rssResponse) AND
        !empty($selectedCategory) AND
        !empty($selectedType))
        <div class="bg-white relative justify-center items-center h-screen pt-6">
            <div class="max-w-xl mx-auto p-4 bg-white">
                <div class="flex flex-col space-y-10">
                    @for($i = 0; ($i < $rssAmount AND $i < count($rssResponse)); $i++)
                        <div
                            class="max-w-3xl {{ $i % 2 == 0 ? 'bg-gray-100' : 'bg-gray-100' }} p-4 rounded-lg shadow-md">
                            <a class="text-base font-medium text-gray-900 hover:text-cyan-700"
                               href="{{ $rssResponse[$i]['link'] }}" target="_blank">
                                {{ $rssResponse[$i]['title'] }}
                            </a>
                            {{-- To fix the Carbon extension bug with accidentally changing pubDate when category was changed --}}
                            @php
                                if(!is_object($rssResponse[$i]['pubDate'])) $rssResponse[$i]['pubDate'] = \Carbon\Carbon::parse($rssResponse[$i]['pubDate']);
                            @endphp
                            <p class="text-base py-3">{{ $rssResponse[$i]['description'] }}</p>
                            <p class="text-sm italic pb-2">{{ $rssResponse[$i]['pubDate']->diffForHumans(['parts' => 1]) }}
                            </p>

                            <div x-data="{ showSpinner: false }" @popup.window="showSpinner = false">
                                <x-button
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50"
                                    x-on:click="if (!showSpinner) {
                                                    showSpinner = true;
                                                    $wire.toggleMessage({{ $i }}, true);
                                                    }
                                                document.body.style.overflow = 'hidden';"
                                    class="hover:bg-cyan-800 refresh-button"
                                >
                                        <span x-show="!showSpinner">
                                            {{ __('Refresh Content') }}
                                        </span>
                                    <div x-show="showSpinner" class="inline-flex items-center m-auto">
                                        <x-loader-spinner/>
                                    </div>
                                </x-button>
                                @error('rssIndex') <span
                                    class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            @if($rssResponse[$i]['show_message'] AND !empty($inOutTexts))
                                <div wire:loading.remove
                                                                         class="fixed inset-0 bg-gray-700 bg-opacity-50 flex justify-center items-center popupwindow">
                                    <div class="max-w-3xl bg-white px-8 py-5 rounded-lg shadow-md relative">
                                        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-500"
                                                x-on:click="$dispatch('popup');
                                                $wire.toggleMessage({{ $i }}, false);
                                                document.body.style.overflow = 'auto';">
                                        <x-svg-cross/>
                                        </button>

                                        <div class="text-base pt-3 pb-5">
                                            <label
                                                class="font-bold text-lg text-gray-800">{{ __('Initial text') }}</label>
                                            <div class="mt-2 text-gray-700 leading-relaxed">
                                                <div class="bg-emerald-50">
                                                    <b class="text-gray-600">{{ __('Title:') }} </b> {{ $inOutTexts['title']['initial'] }}
                                                </div>
                                                <div class="bg-orange-50">
                                                    <b class="text-gray-600">{{ __('Description:') }} </b> {{ $inOutTexts['description']['initial'] }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-base pt-3 pb-5">
                                            <label
                                                class="font-bold text-lg text-gray-800">{{ __('AI Updated text') }}</label>
                                            <div class="mt-2 text-gray-700 leading-relaxed">
                                                <div class="bg-emerald-50">
                                                    <b class="text-gray-600">{{ __('Title:') }}</b> {{ $inOutTexts['title']['updated'] }}
                                                </div>
                                                <div class="bg-orange-50">
                                                    <b class="text-gray-600">{{ __('Description:') }} </b> {{ $inOutTexts['description']['updated'] }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex justify-center">
                                            <x-button autofocus
                                                      class="hover:bg-cyan-800"
                                                      x-on:click="$dispatch('popup');
                                                      $wire.toggleMessage({{ $i }}, false);
                                                      document.body.style.overflow = 'auto';"
                                            >
                                                {{ __('Close') }}
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif
</div>


