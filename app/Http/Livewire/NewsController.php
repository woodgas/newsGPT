<?php declare(strict_types=1, encoding='UTF-8');

namespace App\Http\Livewire;

use Exception;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Http\Clients\RSSToArrayConverter;
use App\Http\Clients\OpenAIClient;

class NewsController extends Component
{
    // Settings
    /**
     * Prefix of rss url.
     *
     * @var string
     */
    private string $rssBaseUrl = 'https://rss.nytimes.com/services/xml/rss/nyt/';

    /**
     * Suffix/extension of rss url.
     *
     * @var string
     */
    private string $rssExt = '.xml';

    /**
     * Location of rss-types array.
     *
     * @var string
     */
    private string $rssTypesPath = '/config/rsstypes.php';

    /**
     * Mounted array of rss-types.
     *
     * @var array
     */
    public array $rssTypes = [];

    /**
     * The response of rss.
     *
     * @var array
     */
    public array $rssResponse = [];

    /**
     * The maximum topics amount per page.
     *
     * @var int
     */
    public int $rssAmount = 10;

    /**
     * Current index of a rss topic.
     *
     * @var mixed|int
     */
    public mixed $rssIndex = 0;

    /**
     * Instructions for the OpenAI requests.
     *
     * @var array|string[]
     */
    private array $instructions = [
        'title' => 'Rewrite the news title, but keep its meaning. The title is: ',
        'description' => 'Rewrite the news description, but keep its meaning.',
    ];

    /**
     * Selected type, associated with the rss-types array.
     *
     * @var string
     */
    public string $selectedType = '';

    /**
     * Selected category, associated with the rss-types array.
     *
     * @var string
     */
    public string $selectedCategory = '';

    /**
     * Input/output texts.
     *
     * Structure:
     *      'title'=> [
     *          'initial' => <string>, 'updated' => <string>],
     *      'description' = > [
     *          'initial' => <string>, 'updated' => <string>]
     * @var array
     */
    public array $inOutTexts = [];


    /**
     * Action of RSS button. Gets an rss response.
     *
     */
    public function convert(): void {

        $this->validateOnly('selectedType');
        $this->validateOnly('selectedCategory');

        if(!empty($this->rssResponse)) {
            $this->rssResponse = [];
        }

        $rssName = $this->rssTypes[$this->selectedType][$this->selectedCategory];
        if($rssName == '') {
            $rssName = $this->selectedCategory;
        }
        $rssUri = $this->rssBaseUrl . $rssName . $this->rssExt;
        $this->rssResponse = $this->rssClient($rssUri);
    }

    /**
     * Action of Update button. Executes the responses to the OpenAI and show/hide initials and results.
     *
     * @param mixed $index
     * @param bool $isRewrite
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function toggleMessage(mixed $index, bool $isRewrite): void
    {
        $this->rssIndex = $index;
        $this->validateOnly('rssIndex');

        if($isRewrite) {
            $this->inOutTexts = $this->rewriteAll($this->rssIndex);
        } else {
            $this->inOutTexts = [];
        }

        foreach ($this->rssResponse as $key => $block) {
            if ($key == $this->rssIndex) {
                $this->rssResponse[$key]['show_message'] = !$this->rssResponse[$key]['show_message'];
            } else {
                $this->rssResponse[$key]['show_message'] = false;
            }
        }
    }

    /**
     * Gets an RSS data.
     *
     * @param string $uri
     * @return array
     */
    private function rssClient(string $uri): array {
        $rssArray = [];
        try {
            $rssArray = RSSToArrayConverter::fromUri($uri)->convert();
        } catch (Exception $e) {
            dd('Error: ' . $e->getMessage());
        }
        return $rssArray;
    }

    /**
     * Runs a request to the OpenAI and gets the response.
     *
     * @param array $input
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function rewriteOne(array $input): string {

        $client = new OpenAIClient(config('services.openai.token'));
        try {
            $responseJson = $client->requestChat($input);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
        $responseArray = json_decode($responseJson, true);
        // Output string
        if($responseArray['choices'][0]['message']['content'] ?? false) {
            return $responseArray['choices'][0]['message']['content'];
        }
        // OpenAI error
        if ($responseArray['error'] ?? false) {
            return 'Error: Type - ' . $responseArray['error']['type'] .
                ' Message - ' . $responseArray['error']['message'];
        }
        return 'Something went wrong...';
    }

    /**
     * Generates an input/output texts.
     *
     * @param int $i
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function rewriteAll(int $i): array {
        $texts = [];
        foreach ($this->instructions as $type => $instruction) {
            $content = $instruction . $this->rssResponse[$i][$type];
            $output = $this->rewriteOne([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $content
                    ],
                ],
//                'model' => 'text-davinci-edit-001',
//                'input' => $this->rssResponse[$i][$type],
//                'instruction' => $instruction,
            ]);
            $texts[$type]['initial'] = $this->rssResponse[$i][$type];
            $texts[$type]['updated'] = $output;
        }
        return $texts;
    }

    // Livewire component automatically executed methods.
    public function render(): object
    {
        return view('livewire.news-controller');
    }

    /**
     * Set settings at the component initiation.
     */
    public function mount(): void {
        $this->rssTypes = require base_path() . $this->rssTypesPath;
    }

    /**
     * Reset all button action.
     */
    public function resetAll(): void {
        $this->reset(['selectedType', 'selectedCategory', 'rssResponse', 'rssIndex']);
        $this->resetErrorBag();
    }

    /**
     * Input data validation rules.
     *
     * @return array[]
     */
    protected function rules(): array
    {
        return [
            'selectedType' => ['required', Rule::prohibitedIf(
                !isset($this->rssTypes[$this->selectedType])
            )],
            'selectedCategory'  => ['required', Rule::prohibitedIf(
                !isset($this->rssTypes[$this->selectedType][$this->selectedCategory])
            )],
            'rssIndex' => ['required', 'int', Rule::prohibitedIf(
                !isset($this->rssResponse[$this->rssIndex])
            )]
        ];
    }

    /**
     * Validates only updated property.
     *
     * @param $propertyName
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}
