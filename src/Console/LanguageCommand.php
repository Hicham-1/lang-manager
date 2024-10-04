<?php

namespace H1ch4m\LangManager\Console;

use Illuminate\Console\Command;

class LanguageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang-manager:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run user migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user_directory = $this->ask('Enter your directory (default is resources/views)');

        $directory = base_path('resources/views');
        if ($user_directory) {
            $user_directory = preg_replace('/\\\\/', '/', $user_directory);

            if (!is_dir($user_directory)) {
                $this->error('The given path is not exists.');
                return;
            }

            $directory = base_path($user_directory);
        }

        $user_lang = $this->ask('Enter your files language (default is en)');

        $lang = 'en';
        if ($user_lang) {
            $lang = $user_lang;
        }
        $this->info('Start, Please wait ...');

        $this->info("Working on all files on directory:");
        $this->info($directory . "\n\n");

        $this->get_all_files($directory, $lang);

        $this->info("\n\nEnd");
    }


    private function get_all_files($directory, $lang)
    {
        $files = [];

        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;
            $path = preg_replace('/\\\\/', '/', $path);

            if (is_dir($path)) {
                $files = array_merge($files, $this->get_all_files($path, $lang));
            } else {
                $files[] = $path;
                $this->info("$path\n");
                $this->scan_and_replace_translations($path, $lang);
            }
        }

        return $files;
    }

    private function scan_and_replace_translations($file, $lang)
    {
        if (pathinfo($file, PATHINFO_EXTENSION) != 'php') {
            return;
        }

        $folder = explode('/', $file);
        if (count($folder) > 1) {
            $folder = $folder[count($folder) - 2];
        } else {
            $folder = $folder[0];
        }

        $translation_file = 'lang/' . $lang . '/' . $folder . '.php';

        $translations = [];
        if (is_file($translation_file)) {
            $translations = require $translation_file;
        } else {
            $directory = dirname($translation_file);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        $content = file_get_contents($file);
        $pattern = '/__\(\s*[\'"]([^\'"]+)[\'"]\s*\)/';

        preg_match_all($pattern, $content, $matches);

        foreach ($matches[1] as $sentence) {
            $new_key = $this->translation_key_prepare($sentence);

            if (!array_key_exists($new_key, $translations)) {
                $translations[$new_key] = $sentence;
                $content = str_replace("__('$sentence')", "__('$folder.$new_key')", $content);
            }

            if ($new_key != $sentence) {
                $content = str_replace("__('$sentence')", "__('$folder.$new_key')", $content);
            }
        }

        file_put_contents($file, $content);
        file_put_contents($translation_file, "<?php\n\nreturn " . var_export($translations, true) . ";\n");
    }

    private function translation_key_prepare($sentence)
    {
        $formatted = strtolower(str_replace(' ', '_', trim($sentence)));

        $words = explode(' ', $sentence);

        if (count($words) > 4) {
            $first_four_words = implode('_', array_slice($words, 0, 4));
            $rest_word_count = count($words) - 4;

            return strtolower($first_four_words) . '_' . $rest_word_count;
        }

        return $formatted;
    }
}
