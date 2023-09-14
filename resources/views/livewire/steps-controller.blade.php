{{--The div tag (or another) must be added. It was a mistake without it.--}}
{{--Unable to set component data. Public property [$selectedType] not found on component: [steps-controller]--}}
<div>

    @if($currentStep === 1)
        <livewire:news-controller />
    @elseif($currentStep === 2)
        <livewire:raw-news :stack="$stack" />
{{--    @elseif($currentStep === 3)--}}
{{--        <livewire:step3 />--}}
    @endif
</div>
