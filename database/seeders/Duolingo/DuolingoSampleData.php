<?php

namespace Database\Seeders\Duolingo;

/**
 * Sample content for the free Duolingo mock: exactly 3 questions per question type.
 * All text is original PerfectMocks practice content, not official test material.
 *
 * Conventions:
 *  - Blanks: "[pract|ice]" = visible prefix "pract" + expected answer "ice".
 *    "[|word]" = the whole word is typed.
 *  - Options: the first option listed is the correct one; the seeder stores a
 *    deterministic per-question order so the answer is not always first.
 *  - Audio: list of [voice, text] segments. Voice is F or M.
 */
class DuolingoSampleData
{
    /** Instructions shown on each question, by question type tag. */
    public const INSTRUCTIONS = [
        'RCT'  => 'Type the missing letters to complete each word in the paragraph.',
        'RSL'  => 'Decide whether this is a real English word.',
        'IRCS' => 'Select the option that best completes the sentence.',
        'IRCP' => 'Select the sentence that best fills the gap in the passage.',
        'IRHA' => 'Click and drag across the words in the passage that answer the question.',
        'IRII' => 'Select the best answer.',
        'IRTP' => 'Select the best title for the passage.',
        'VIC'  => 'Type the word that best completes the sentence.',
        'LSL'  => 'Listen to the word and decide whether it is a real English word.',
        'LTY'  => 'Listen to the sentence and type exactly what you hear.',
        'ILLR' => 'Listen to the speaker and select the most appropriate response.',
        'ILLC' => 'Listen to the conversation and select the best answer.',
        'ILSC' => 'Write a summary of the conversation you just heard.',
        'WAP'  => 'Write a description of the image.',
        'IWR'  => 'Write a developed response to the prompt. A follow-up prompt appears in Part 2.',
        'WSM'  => 'Write an extended response to the prompt.',
        'SAP'  => 'Record yourself describing the image.',
        'RTS'  => 'Read the prompt, then record your spoken response.',
        'LTS'  => 'Listen to the prompt, then record your spoken response.',
        'ISP'  => 'Listen to each question and record your answer before the time runs out.',
        'SSM'  => 'Record an extended spoken response to the prompt.',
    ];

    /** Read and Complete (C-Test). */
    public static function readAndComplete(): array
    {
        return [
            'Learning a new language requires consistent [pract|ice] and patience. Most learners make steady [prog|ress] when they study for a short [ti|me] every day. Reading simple [boo|ks] and listening to [mus|ic] can also build useful [voca|bulary] without much [eff|ort].',
            'Rivers have shaped human [hist|ory] for thousands of years. Early farmers settled [ne|ar] them because the water [mad|e] the soil rich. Later, rivers became important [rou|tes] for trade, and many great [cit|ies] grew along their [ban|ks] as a [res|ult].',
            'Good sleep is essential for [hea|lth]. During the night, the [bra|in] organizes new information and the [bo|dy] repairs itself. People who [re|st] well usually find it [eas|ier] to concentrate, and they are less [lik|ely] to feel tired at [wo|rk].',
        ];
    }

    /** Read and Select: word => is it real? */
    public static function readAndSelect(): array
    {
        return [
            ['word' => 'harvest',  'answer' => 'yes'],
            ['word' => 'flombish', 'answer' => 'no'],
            ['word' => 'candid',   'answer' => 'yes'],
        ];
    }

    /** Vocabulary in Context. */
    public static function vocabularyInContext(): array
    {
        return [
            'The scientist repeated the [|experiment] three times to confirm that the results were reliable.',
            'After the long drought, farmers were relieved when the [|rain] finally arrived.',
            'She thanked the audience and left the [|stage] after finishing her speech.',
        ];
    }

    /**
     * Interactive Reading: one connected passage per entry, with 5 subtasks each.
     * "partial" and "gapped" reveal the passage progressively so that earlier
     * subtasks never display their own answer.
     */
    public static function interactiveReading(): array
    {
        return [
            [
                'title'   => 'Interactive Reading 1',
                'passage' => "Urban beekeeping has grown rapidly over the past decade. Many city residents now keep hives on rooftops and balconies because they want to support local pollinators. Surprisingly, bees often do well in cities, where parks and gardens offer a wide variety of flowers throughout the season.\n\nHowever, the trend has created new challenges. When too many hives are placed in a small area, the bees must compete for a limited supply of nectar, and wild pollinators may lose out. For this reason, some cities now require beekeepers to register their hives and to plant more flowers nearby.",
                'partial' => 'Urban beekeeping has grown rapidly over the past decade. Many city residents now keep hives on rooftops and balconies because they want to _____.',
                'sentence_q' => 'Select the best option for the missing part of the sentence.',
                'sentence'   => ['support local pollinators', 'avoid paying high rents', 'sell their apartments', 'study ancient languages'],
                'gapped'  => "Urban beekeeping has grown rapidly over the past decade. Many city residents now keep hives on rooftops and balconies because they want to support local pollinators. Surprisingly, bees often do well in cities, where parks and gardens offer a wide variety of flowers throughout the season.\n\n_____ When too many hives are placed in a small area, the bees must compete for a limited supply of nectar, and wild pollinators may lose out. For this reason, some cities now require beekeepers to register their hives and to plant more flowers nearby.",
                'gap'     => ['However, the trend has created new challenges.', 'Honey is sold in most city markets.', 'Bees cannot survive in cold weather.', 'Most rooftops are too small for gardens.'],
                'highlight_q' => 'Why do bees often do well in cities?',
                'highlight'   => 'parks and gardens offer a wide variety of flowers throughout the season',
                'idea_q'  => 'What is the main idea of the passage?',
                'idea'    => ['Urban beekeeping is popular, but it needs careful management.', 'Bees produce more honey in cities than in the countryside.', 'Cities should ban beekeeping on rooftops.', 'Wild pollinators have disappeared from most cities.'],
                'titles'  => ['City Bees: A Growing Trend with Growing Pains', 'How to Cook with Honey', 'The Life Cycle of the Butterfly', 'Why Rooftops Leak'],
            ],
            [
                'title'   => 'Interactive Reading 2',
                'passage' => "For many years, naps were seen as a sign of laziness. Recent research suggests the opposite. A short sleep in the afternoon can improve attention and help people remember what they have just learned.\n\nThe length of the nap matters, though. Sleeping for about twenty minutes tends to leave people feeling refreshed, while longer naps can cause grogginess that lasts for some time after waking. As a result, several companies have created quiet rooms where employees can rest briefly during the working day.",
                'partial' => 'For many years, naps were seen as a sign of laziness. Recent research suggests the opposite. A short sleep in the afternoon can _____.',
                'sentence_q' => 'Select the best option for the missing part of the sentence.',
                'sentence'   => ['improve attention and memory', 'replace sleep at night', 'cause people to lose weight', 'make people less friendly'],
                'gapped'  => "For many years, naps were seen as a sign of laziness. Recent research suggests the opposite. A short sleep in the afternoon can improve attention and help people remember what they have just learned.\n\n_____ Sleeping for about twenty minutes tends to leave people feeling refreshed, while longer naps can cause grogginess that lasts for some time after waking. As a result, several companies have created quiet rooms where employees can rest briefly during the working day.",
                'gap'     => ['The length of the nap matters, though.', 'Coffee is more popular than tea.', 'Most offices open at nine in the morning.', 'Children usually dislike going to bed.'],
                'highlight_q' => 'What can happen after a long nap?',
                'highlight'   => 'grogginess that lasts for some time after waking',
                'idea_q'  => 'What is the main idea of the passage?',
                'idea'    => ['Short naps can bring real benefits to people at work.', 'Everyone should sleep for longer at night.', 'Companies should make employees work fewer hours.', 'Long naps are healthier than short ones.'],
                'titles'  => ['The Surprising Power of a Short Nap', 'A History of the Office', 'Choosing a Comfortable Mattress', 'Why Children Dream'],
            ],
            [
                'title'   => 'Interactive Reading 3',
                'passage' => "The first bicycles, built in the early nineteenth century, had no pedals. Riders pushed themselves along with their feet, and the machines were heavy and difficult to steer. Pedals were added some decades later, which made longer journeys possible.\n\nThe real breakthrough came with the safety bicycle in the 1880s. It had two wheels of equal size and a chain that drove the rear wheel, so riders no longer risked a dangerous fall from a great height. Cycling soon became an affordable way for ordinary people to travel, and it gave many of them a new sense of independence.",
                'partial' => 'The first bicycles, built in the early nineteenth century, had no pedals. Riders _____.',
                'sentence_q' => 'Select the best option for the missing part of the sentence.',
                'sentence'   => ['pushed themselves along with their feet', 'were pulled along by horses', 'used small engines to move', 'travelled only on railway tracks'],
                'gapped'  => "The first bicycles, built in the early nineteenth century, had no pedals. Riders pushed themselves along with their feet, and the machines were heavy and difficult to steer. Pedals were added some decades later, which made longer journeys possible.\n\n_____ It had two wheels of equal size and a chain that drove the rear wheel, so riders no longer risked a dangerous fall from a great height. Cycling soon became an affordable way for ordinary people to travel, and it gave many of them a new sense of independence.",
                'gap'     => ['The real breakthrough came with the safety bicycle in the 1880s.', 'Today, many cities have special lanes for cyclists.', 'Horses remained expensive to feed and house.', 'Early cars were also difficult to steer.'],
                'highlight_q' => 'Why was the safety bicycle less dangerous?',
                'highlight'   => 'riders no longer risked a dangerous fall from a great height',
                'idea_q'  => 'What is the main idea of the passage?',
                'idea'    => ['Design improvements turned the bicycle into practical, everyday transport.', 'The first bicycles were faster than later models.', 'Cycling was only ever popular among wealthy people.', 'Pedals were the least important part of the bicycle.'],
                'titles'  => ['From Foot Power to Freedom: The Rise of the Bicycle', 'The Fastest Cyclists in the World', 'How to Repair a Flat Tire', 'Life Before Railways'],
            ],
        ];
    }

    /** Listen and Select. */
    public static function listenAndSelect(): array
    {
        return [
            ['word' => 'journey', 'answer' => 'yes'],
            ['word' => 'plonter', 'answer' => 'no'],
            ['word' => 'measure', 'answer' => 'yes'],
        ];
    }

    /** Listen and Type. */
    public static function listenAndType(): array
    {
        return [
            ["The university library closes at nine o'clock every evening.", ['The university library closes at 9 o\'clock every evening.']],
            ['Please remember to submit your assignment before the end of the week.', []],
            ['The museum offers free guided tours on the first Sunday of each month.', []],
        ];
    }

    /** Interactive Listening: one scenario per entry, with 3 connected subtasks each. */
    public static function interactiveListening(): array
    {
        return [
            [
                'title'    => 'Interactive Listening 1',
                'scenario' => 'You are a student talking to a library assistant about a book you need.',
                'respond_audio' => [['F', 'Hello, how can I help you today?']],
                'respond'  => ['I am looking for a book for my history class.', 'The weather has been lovely this week.', 'I usually have lunch at one o\'clock.', 'My brother plays football on Saturdays.'],
                'complete_audio' => [
                    ['F', 'I am sorry, the only copy of that book is on loan until Friday.'],
                    ['M', 'Oh, I need it for an essay that is due on Monday. Is there anything I can do?'],
                    ['F', 'You can reserve it now, and we will hold it at the front desk for you. There is also an online version you can read today.'],
                    ['M', 'That is perfect. I will reserve the book and start with the online version.'],
                ],
                'complete_q' => 'What does the student decide to do?',
                'complete' => ['Reserve the book and read the online version', 'Buy a copy from a bookshop', 'Ask the teacher for more time', 'Choose a different essay topic'],
            ],
            [
                'title'    => 'Interactive Listening 2',
                'scenario' => 'You are a new student asking an advisor about changing a course.',
                'respond_audio' => [['M', 'Welcome. I understand you would like to talk about your timetable?']],
                'respond'  => ['Yes, I would like to change one of my courses.', 'No, I have never been to the swimming pool.', 'Thanks, I already had breakfast.', 'It takes twenty minutes by bus.'],
                'complete_audio' => [
                    ['M', 'You have until the end of next week to change a course without paying a fee.'],
                    ['F', 'That is good to know. I would like to move from statistics to economics, because statistics clashes with my part-time job.'],
                    ['M', 'Economics still has places. Fill in this short form and ask your statistics tutor to sign it.'],
                    ['F', 'Thank you. I will see my tutor this afternoon.'],
                ],
                'complete_q' => 'Why does the student want to change course?',
                'complete' => ['The course clashes with a part-time job', 'The course is too difficult', 'The tutor has left the university', 'The fee is too expensive'],
            ],
            [
                'title'    => 'Interactive Listening 3',
                'scenario' => 'You are talking to a classmate about preparing a group presentation.',
                'respond_audio' => [['F', 'Have you had a chance to look at our presentation yet?']],
                'respond'  => ['Yes, I read it last night and have a few ideas.', 'Yes, the train usually leaves from platform two.', 'No, I do not like spicy food.', 'I bought these shoes last summer.'],
                'complete_audio' => [
                    ['F', 'I think our slides have too much text. People will stop listening.'],
                    ['M', 'I agree. Why don\'t we replace some of it with charts, and keep the details in our notes?'],
                    ['F', 'Good idea. Could you make the charts? I will shorten the text and then we can practice together on Thursday.'],
                    ['M', 'Sure. I will send you the charts by Wednesday evening.'],
                ],
                'complete_q' => 'What will the man do?',
                'complete' => ['Make the charts and send them by Wednesday', 'Give the whole presentation alone', 'Shorten the text on the slides', 'Cancel the practice on Thursday'],
            ],
        ];
    }

    /** Write About the Photo / Speak About the Photo share the image bank. */
    public static function writePhotos(): array
    {
        return [
            ['image' => 'library.svg', 'alt' => 'Students reading and using laptops at a table in a library.'],
            ['image' => 'market.svg',  'alt' => 'People shopping at outdoor fruit and vegetable stalls.'],
            ['image' => 'park.svg',    'alt' => 'A family having a picnic in a park beside a tree.'],
        ];
    }

    public static function speakPhotos(): array
    {
        return [
            ['image' => 'train.svg',   'alt' => 'Passengers waiting on a platform as a train arrives.'],
            ['image' => 'kitchen.svg', 'alt' => 'Two people cooking together in a kitchen.'],
            ['image' => 'beach.svg',   'alt' => 'People relaxing and playing on a sunny beach.'],
        ];
    }

    /** Interactive Writing: [initial prompt, follow-up prompt]. */
    public static function interactiveWriting(): array
    {
        return [
            ['Some people prefer to study alone, while others prefer to study in groups. Which do you prefer, and why?', 'Describe a time when your preferred way of studying did not work well. What did you do about it?'],
            ['Should universities require all students to take a course outside their main subject? Explain your opinion.', 'What kind of course would you recommend to a student of your own subject, and how would it help them?'],
            ['Describe a skill you would like to learn in the next five years and explain why it matters to you.', 'What might make it difficult to learn this skill, and how would you overcome those difficulties?'],
        ];
    }

    public static function writingSamples(): array
    {
        return [
            'Technology has changed the way people communicate. Do you think these changes have been mostly positive or mostly negative? Support your opinion with examples.',
            'Many cities are limiting the use of private cars in their centers. Discuss the advantages and disadvantages of this policy.',
            'Some people believe that success depends mainly on hard work, while others believe it depends on luck. What is your view?',
        ];
    }

    public static function readThenSpeak(): array
    {
        return [
            'Talk about a teacher who has influenced you. Who is this person, what did they teach you, and why was it important?',
            'Describe a place in your city or town that you would recommend to a visitor. Explain what makes it special.',
            'Talk about a difficult decision you have made. What were your options, and how did you decide?',
        ];
    }

    public static function listenThenSpeak(): array
    {
        return [
            [['F', 'Tell me about a book or a film that you enjoyed recently. What was it about, and why did you like it?']],
            [['M', 'Do you think it is better to live in a big city or in a small town? Explain your reasons.']],
            [['F', 'Describe a tradition that is important in your family or community. How do people celebrate it?']],
        ];
    }

    /** Interactive Speaking: scenario + 6 connected turns. */
    public static function interactiveSpeaking(): array
    {
        return [
            [
                'intro' => 'A university advisor is asking about your studies.',
                'voice' => 'F',
                'turns' => [
                    'What subject are you most interested in studying, and why?',
                    'How did you first become interested in that subject?',
                    'What do you find most challenging about studying it?',
                    'How do you usually prepare for an important exam?',
                    'What job would you like to have after you finish your studies?',
                    'What advice would you give to a new student in your field?',
                ],
            ],
            [
                'intro' => 'A friend is asking about how you spend your free time.',
                'voice' => 'M',
                'turns' => [
                    'What do you enjoy doing most in your free time?',
                    'How long have you been interested in this activity?',
                    'Do you prefer doing it alone or with other people? Why?',
                    'Has this hobby taught you anything useful?',
                    'Is there a new hobby you would like to try?',
                    'Do you think people today have enough free time? Why or why not?',
                ],
            ],
            [
                'intro' => 'A researcher is asking about travel and transport where you live.',
                'voice' => 'F',
                'turns' => [
                    'How do you usually travel around your city or town?',
                    'What do you like or dislike about this way of traveling?',
                    'Tell me about a memorable journey that you have taken.',
                    'How has transport in your area changed in recent years?',
                    'What one change would most improve travel where you live?',
                    'Do you think people will travel more or less in the future? Why?',
                ],
            ],
        ];
    }

    public static function speakingSamples(): array
    {
        return [
            'Some people think that children should begin learning a foreign language at a very young age. Do you agree? Explain your reasons and give examples.',
            'Describe an achievement you are proud of. Explain what you did, what challenges you faced, and what you learned from the experience.',
            'If you could change one thing about the education system in your country, what would it be? Explain why this change is needed.',
        ];
    }
}
