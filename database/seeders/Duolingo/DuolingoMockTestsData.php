<?php

namespace Database\Seeders\Duolingo;

/**
 * Content for free Duolingo Mock Tests 2–5. Each test has its own questions and a
 * different number of questions per section. Shapes follow DuolingoSampleData
 * (see the BuildsDuolingoTests trait for the format of each key).
 *
 * All text is original PerfectMocks practice content, not official test material.
 *
 * Questions per section:
 *   Test 2  Reading 12 · Listening 8  · Writing 3 · Speaking 5  = 28
 *   Test 3  Reading 18 · Listening 8  · Writing 4 · Speaking 7  = 37
 *   Test 4  Reading 11 · Listening 12 · Writing 4 · Speaking 7  = 34
 *   Test 5  Reading 17 · Listening 12 · Writing 5 · Speaking 7  = 41
 */
class DuolingoMockTestsData
{
    /** @return array<int, array{number: int, content: array}> */
    public static function tests(): array
    {
        return [
            ['number' => 2, 'content' => self::test2()],
            ['number' => 3, 'content' => self::test3()],
            ['number' => 4, 'content' => self::test4()],
            ['number' => 5, 'content' => self::test5()],
        ];
    }

    /* ── Test 2 ─────────────────────────────────────────────────────── */

    private static function test2(): array
    {
        return [
            'rct' => [
                'Many people enjoy cooking at [ho|me] because it saves [mon|ey] and is often [heal|thier] than eating out. Planning [mea|ls] for the week also reduces [wa|ste], since you only [b|uy] what you need.',
                'The museum opened a new [exhi|bition] about ancient [tr|ade] routes. Visitors can [se|e] maps, coins and [too|ls] that merchants once [carr|ied] across deserts and [sea|s].',
            ],
            'rsl' => [
                ['word' => 'garden',  'answer' => 'yes'],
                ['word' => 'drobble', 'answer' => 'no'],
                ['word' => 'patient', 'answer' => 'yes'],
            ],
            'ir' => [
                [
                    'title'   => 'Test 2 · Interactive Reading 1',
                    'passage' => "Community gardens are shared spaces where neighbours grow vegetables, fruit and flowers together. They are often built on empty land that would otherwise be unused. Besides producing fresh food, these gardens give people a reason to spend time outdoors and to meet others who live nearby.\n\nResearchers have found that community gardens can also make neighbourhoods safer. When residents care for a shared space, they tend to keep a closer eye on their streets. In some cities, local governments now provide water and tools to encourage more of these projects.",
                    'partial' => 'Community gardens are shared spaces where neighbours grow vegetables, fruit and flowers together. They are often built on _____.',
                    'sentence_q' => 'Select the best option for the missing part of the sentence.',
                    'sentence'   => ['empty land that would otherwise be unused', 'the roofs of busy shopping centres', 'land owned by large farms', 'islands far from the city'],
                    'gapped'  => "Community gardens are shared spaces where neighbours grow vegetables, fruit and flowers together. They are often built on empty land that would otherwise be unused. Besides producing fresh food, these gardens give people a reason to spend time outdoors and to meet others who live nearby.\n\n_____ When residents care for a shared space, they tend to keep a closer eye on their streets. In some cities, local governments now provide water and tools to encourage more of these projects.",
                    'gap'     => ['Researchers have found that community gardens can also make neighbourhoods safer.', 'Most vegetables need plenty of sunlight to grow.', 'Flowers are usually more expensive than fruit.', 'Many people prefer to shop at large supermarkets.'],
                    'highlight_q' => 'Why do residents keep a closer eye on their streets?',
                    'highlight'   => 'residents care for a shared space',
                    'idea_q'  => 'What is the main idea of the passage?',
                    'idea'    => ['Community gardens benefit neighbourhoods in several ways.', 'Gardening is too difficult for most city residents.', 'Local governments should sell empty land.', 'Fresh food is cheaper in supermarkets than in gardens.'],
                    'titles'  => ['Growing More Than Vegetables', 'How to Build a Swimming Pool', 'The History of the Supermarket', 'Why Cities Need More Cars'],
                ],
            ],
            'vic' => [
                'Please [|turn/switch] off the lights when you leave the room.',
                'The children were [|excited/thrilled] to visit the zoo for the first time.',
            ],
            'lsl' => [
                ['word' => 'window',  'answer' => 'yes'],
                ['word' => 'glimber', 'answer' => 'no'],
                ['word' => 'basket',  'answer' => 'yes'],
            ],
            'lty' => [
                ['Students must register for their courses before the end of August.', []],
                ['The bus to the city centre leaves every fifteen minutes.', ['The bus to the city centre leaves every 15 minutes.']],
            ],
            'il' => [
                [
                    'title'    => 'Test 2 · Interactive Listening 1',
                    'scenario' => "You are calling a dentist's office to make an appointment.",
                    'respond_audio' => [['F', 'Good morning, City Dental Clinic. How can I help you?']],
                    'respond'  => ['Hi, I would like to make an appointment for a check-up.', 'I usually take the train to work.', 'My favourite colour is blue.', 'It was raining all day yesterday.'],
                    'complete_audio' => [
                        ['F', 'We have an opening on Thursday at ten, or Friday afternoon at three.'],
                        ['M', 'Thursday morning is difficult because I have a lecture. Friday at three works for me.'],
                        ['F', 'Great, I have booked you for Friday at three. Please arrive ten minutes early to fill in a form.'],
                        ['M', 'Thank you, I will be there.'],
                    ],
                    'complete_q' => 'When is the appointment?',
                    'complete' => ['Friday at three', 'Thursday at ten', 'Friday at ten', 'Thursday at three'],
                ],
            ],
            'wap' => [
                ['image' => 'cafe.svg', 'alt' => 'People sitting at tables in a busy café while a barista makes coffee.'],
            ],
            'iwr' => [
                ['Some people believe that homework should be banned in primary schools. Do you agree or disagree? Explain why.', 'Describe a piece of homework you remember well. What did you learn from it?'],
            ],
            'wsm' => [
                'Many students now take online courses instead of attending classes in person. Discuss the advantages and disadvantages of online learning.',
            ],
            'sap' => [
                ['image' => 'classroom.svg', 'alt' => 'A teacher pointing at a whiteboard while students listen at their desks.'],
            ],
            'rts' => [
                'Talk about a holiday that you enjoyed. Where did you go, what did you do, and why was it memorable?',
            ],
            'lts' => [
                [['F', 'What is your favourite season of the year, and what do you like to do during that season?']],
            ],
            'isp' => [
                [
                    'intro' => 'A careers advisor is asking about your plans.',
                    'voice' => 'F',
                    'turns' => [
                        'What kind of work are you interested in?',
                        'What skills do you already have for that kind of work?',
                        'What experience would you like to gain in the next few years?',
                        'Who has helped you most with your career plans?',
                        'What might make it difficult to reach your goals?',
                        'Where do you see yourself in ten years?',
                    ],
                ],
            ],
            'ssm' => [
                'Some people believe that every student should learn to cook at school. Do you agree? Give reasons and examples.',
            ],
        ];
    }

    /* ── Test 3 ─────────────────────────────────────────────────────── */

    private static function test3(): array
    {
        return [
            'rct' => [
                'Volunteering can benefit both the [commu|nity] and the volunteer. People who [he|lp] others often [rep|ort] feeling happier, and they [lea|rn] new skills that may be [use|ful] in their future [care|ers].',
            ],
            'rsl' => [
                ['word' => 'gravel',  'answer' => 'yes'],
                ['word' => 'snerkin', 'answer' => 'no'],
                ['word' => 'thunder', 'answer' => 'yes'],
                ['word' => 'brolish', 'answer' => 'no'],
            ],
            'ir' => [
                [
                    'title'   => 'Test 3 · Interactive Reading 1',
                    'passage' => "Before the fifteenth century, books in Europe were copied by hand. The work was slow and expensive, so only wealthy people and religious institutions could afford to own them. Around 1440, Johannes Gutenberg developed a printing press that used movable metal type.\n\nThe new machine could produce hundreds of pages in a single day. As books became cheaper, more people learned to read, and ideas spread far more quickly than before. Many historians consider the printing press one of the most important inventions in human history.",
                    'partial' => 'Before the fifteenth century, books in Europe were copied by hand. The work was slow and expensive, so _____.',
                    'sentence_q' => 'Select the best option for the missing part of the sentence.',
                    'sentence'   => ['only wealthy people and religious institutions could afford to own them', 'most families owned several copies', 'books were given away for free', 'children wrote their own books at school'],
                    'gapped'  => "Before the fifteenth century, books in Europe were copied by hand. The work was slow and expensive, so only wealthy people and religious institutions could afford to own them. Around 1440, Johannes Gutenberg developed a printing press that used movable metal type.\n\n_____ As books became cheaper, more people learned to read, and ideas spread far more quickly than before. Many historians consider the printing press one of the most important inventions in human history.",
                    'gap'     => ['The new machine could produce hundreds of pages in a single day.', 'Paper was first made in ancient China.', 'Gutenberg was born in a small village.', 'Some books are still copied by hand today.'],
                    'highlight_q' => 'What happened as books became cheaper?',
                    'highlight'   => 'more people learned to read',
                    'idea_q'  => 'What is the main idea of the passage?',
                    'idea'    => ['The printing press made books and ideas widely available.', 'Copying books by hand was a popular hobby.', 'Gutenberg was a wealthy religious leader.', 'Metal type was more expensive than handwriting.'],
                    'titles'  => ['The Machine That Spread Ideas', 'A Guide to Metal Recycling', 'Famous Libraries of Europe', 'Learning to Write by Hand'],
                ],
                [
                    'title'   => 'Test 3 · Interactive Reading 2',
                    'passage' => "Deserts receive very little rain, yet many plants manage to survive there. The cactus is a well-known example. Its thick stem stores water for long periods, and its sharp spines protect it from animals that might try to drink from it.\n\nOther desert plants have different strategies. Some have roots that spread wide just below the surface, so they can collect water quickly after a brief storm. Others remain as seeds in the ground for years and only grow when enough rain finally falls.",
                    'partial' => 'Deserts receive very little rain, yet many plants manage to survive there. The cactus is a well-known example. Its thick stem _____.',
                    'sentence_q' => 'Select the best option for the missing part of the sentence.',
                    'sentence'   => ['stores water for long periods', 'grows taller than most trees', 'changes colour every week', 'is soft enough to eat'],
                    'gapped'  => "Deserts receive very little rain, yet many plants manage to survive there. The cactus is a well-known example. Its thick stem stores water for long periods, and its sharp spines protect it from animals that might try to drink from it.\n\n_____ Some have roots that spread wide just below the surface, so they can collect water quickly after a brief storm. Others remain as seeds in the ground for years and only grow when enough rain finally falls.",
                    'gap'     => ['Other desert plants have different strategies.', 'Many tourists visit deserts in winter.', 'Rain forests receive a great deal of rain.', 'Camels can walk for days without water.'],
                    'highlight_q' => 'How do some plants collect water quickly after a storm?',
                    'highlight'   => 'roots that spread wide just below the surface',
                    'idea_q'  => 'What is the main idea of the passage?',
                    'idea'    => ['Desert plants have developed several ways to survive with little water.', 'Cactuses are dangerous to people.', 'Deserts are becoming wetter every year.', 'Seeds cannot survive in dry ground.'],
                    'titles'  => ['Surviving Without Rain', 'The Tallest Trees on Earth', 'Planning a Desert Holiday', 'Why Animals Avoid Water'],
                ],
            ],
            'vic' => [
                'He forgot his umbrella, so he got [|wet] on the way home.',
                'The train was [|delayed] by thirty minutes because of the snow.',
                'Water [|boils] at one hundred degrees Celsius at sea level.',
            ],
            'lsl' => [
                ['word' => 'travel',  'answer' => 'yes'],
                ['word' => 'snorple', 'answer' => 'no'],
            ],
            'lty' => [
                ['Please bring a photo ID to the examination room.', []],
                ['The new sports centre will open next spring.', []],
                ['Our professor posted the reading list on the course website.', []],
            ],
            'il' => [
                [
                    'title'    => 'Test 3 · Interactive Listening 1',
                    'scenario' => 'You are a student asking a lab technician about equipment.',
                    'respond_audio' => [['M', 'Hi there. Are you here to borrow something from the lab?']],
                    'respond'  => ['Yes, I need a microscope for my biology project.', 'No, I have already eaten lunch.', 'The library closes at nine.', 'I am from a small town.'],
                    'complete_audio' => [
                        ['M', 'You can borrow a microscope for up to three days, but you need to show your student card.'],
                        ['F', 'I left my card at home today. Can I bring it tomorrow?'],
                        ['M', 'Of course. I will keep one aside for you until tomorrow at noon.'],
                        ['F', 'Thank you, I will come back in the morning.'],
                    ],
                    'complete_q' => 'Why can the student not borrow the microscope today?',
                    'complete' => ['She does not have her student card', 'All the microscopes are broken', 'The lab is closing early', 'She has not paid a fee'],
                ],
            ],
            'wap' => [
                ['image' => 'airport.svg', 'alt' => 'Travellers with suitcases in an airport hall, with a plane visible through the window.'],
                ['image' => 'market.svg',  'alt' => 'People shopping at outdoor fruit and vegetable stalls.'],
            ],
            'iwr' => [
                ['Would you rather live in a house with a large garden or an apartment in the city centre? Explain your choice.', 'What would you change about the place where you live now, and why?'],
            ],
            'wsm' => [
                'Some people think that governments should spend more money on space exploration. Others think the money should be used to solve problems on Earth. Discuss both views and give your opinion.',
            ],
            'sap' => [
                ['image' => 'farm.svg',    'alt' => 'A farmer driving a tractor past a barn and fields of crops.'],
                ['image' => 'kitchen.svg', 'alt' => 'Two people cooking together in a kitchen.'],
            ],
            'rts' => [
                'Describe a skill that you learned outside of school. How did you learn it, and how do you use it now?',
            ],
            'lts' => [
                [['M', 'Describe a meal that you enjoy cooking or eating. Why do you like it?']],
                [['F', 'Some people prefer to study in the morning, while others prefer the evening. Which do you prefer, and why?']],
            ],
            'isp' => [
                [
                    'intro' => 'A new classmate is asking about your hometown.',
                    'voice' => 'M',
                    'turns' => [
                        'Where are you from originally?',
                        'What is your hometown best known for?',
                        'What do young people do there in their free time?',
                        'How has your hometown changed in recent years?',
                        'What do you miss most about it when you are away?',
                        'Would you recommend it to tourists? Why or why not?',
                    ],
                ],
            ],
            'ssm' => [
                'Describe a challenge you faced while learning something new. How did you deal with it, and what did you learn?',
            ],
        ];
    }

    /* ── Test 4 ─────────────────────────────────────────────────────── */

    private static function test4(): array
    {
        return [
            'rct' => [
                'Public libraries have changed a [gr|eat] deal. Besides [len|ding] books, many now offer free [inte|rnet] access, language [clas|ses] and quiet [spa|ces] where students can [stu|dy].',
                'Weather forecasts are more [accu|rate] today than in the [pa|st]. Satellites [col|lect] huge amounts of [da|ta], and powerful computers [pro|cess] it to [pre|dict] storms days in advance.',
                'Learning to play a musical [instr|ument] takes time. Beginners should [prac|tise/tice] a little every [d|ay] rather than for many [ho|urs] once a week, because regular [eff|ort] builds [mem|ory] more effectively.',
            ],
            'rsl' => [
                ['word' => 'quandle', 'answer' => 'no'],
                ['word' => 'margin',  'answer' => 'yes'],
            ],
            'ir' => [
                [
                    'title'   => 'Test 4 · Interactive Reading 1',
                    'passage' => "Several companies have experimented with a four-day working week. Employees work fewer hours but receive the same salary. Supporters argue that rested workers are more focused and make fewer mistakes, so productivity does not fall.\n\nThe results of these trials have been mixed. Some firms reported happier staff and lower costs, while others found it difficult to serve customers who expected them to be open five days a week. Experts suggest that the idea works best in jobs where tasks can be completed independently.",
                    'partial' => 'Several companies have experimented with a four-day working week. Employees work fewer hours but _____.',
                    'sentence_q' => 'Select the best option for the missing part of the sentence.',
                    'sentence'   => ['receive the same salary', 'must work at weekends', 'are paid only for three days', 'lose their holidays'],
                    'gapped'  => "Several companies have experimented with a four-day working week. Employees work fewer hours but receive the same salary. Supporters argue that rested workers are more focused and make fewer mistakes, so productivity does not fall.\n\n_____ Some firms reported happier staff and lower costs, while others found it difficult to serve customers who expected them to be open five days a week. Experts suggest that the idea works best in jobs where tasks can be completed independently.",
                    'gap'     => ['The results of these trials have been mixed.', 'Most offices close at six in the evening.', 'Salaries have risen quickly in recent years.', 'Many people enjoy working from cafés.'],
                    'highlight_q' => 'What problem did some firms face?',
                    'highlight'   => 'difficult to serve customers who expected them to be open five days a week',
                    'idea_q'  => 'What is the main idea of the passage?',
                    'idea'    => ['A shorter working week has advantages but does not suit every business.', 'All companies should close on Fridays.', 'Workers make more mistakes when they rest.', 'Customers prefer businesses that open at weekends.'],
                    'titles'  => ['Is Four Days Enough?', 'The Best Cafés for Working', 'A History of the Weekend', 'How to Ask for a Pay Rise'],
                ],
            ],
            'vic' => [
                'She [|borrowed] three books from the library last week.',
            ],
            'lsl' => [
                ['word' => 'mirror',  'answer' => 'yes'],
                ['word' => 'blenter', 'answer' => 'no'],
                ['word' => 'kitchen', 'answer' => 'yes'],
                ['word' => 'drafful', 'answer' => 'no'],
            ],
            'lty' => [
                ['Remember to water the plants while I am away.', ["Remember to water the plants while I'm away."]],
                ['The cafe on the corner serves the best coffee in town.', []],
            ],
            'il' => [
                [
                    'title'    => 'Test 4 · Interactive Listening 1',
                    'scenario' => 'You are talking to a neighbour about a lost cat.',
                    'respond_audio' => [['F', 'Excuse me, have you seen a small grey cat around here?']],
                    'respond'  => ['I think I saw one near the park this morning.', 'I usually buy bread at the bakery.', 'My brother is studying engineering.', 'I prefer summer to winter.'],
                    'complete_audio' => [
                        ['F', 'Her name is Misty. She went missing two days ago.'],
                        ['M', 'I saw a grey cat sitting under the bench by the playground. Shall we go and look together?'],
                        ['F', 'Yes, please. I will bring her favourite treats.'],
                        ['M', 'Good idea. Let us meet at the park gate in five minutes.'],
                    ],
                    'complete_q' => 'What will they do next?',
                    'complete' => ['Meet at the park gate to look for the cat', 'Call the police', 'Put up posters around town', 'Buy a new cat'],
                ],
                [
                    'title'    => 'Test 4 · Interactive Listening 2',
                    'scenario' => 'You are asking a librarian about study rooms.',
                    'respond_audio' => [['M', 'Hello. Do you need help finding anything?']],
                    'respond'  => ['Yes, I would like to book a study room for my group.', 'I think it will snow tomorrow.', 'No, I have never been to Paris.', 'The concert starts at eight.'],
                    'complete_audio' => [
                        ['M', 'Study rooms can be booked for two hours at a time. Rooms on the third floor have whiteboards.'],
                        ['F', 'We need a whiteboard for our presentation practice. Is a room free this afternoon?'],
                        ['M', 'Room 304 is free from two until four. I will book it under your name.'],
                        ['F', 'Perfect, thank you.'],
                    ],
                    'complete_q' => 'Why does the group want a room on the third floor?',
                    'complete' => ['The rooms there have whiteboards', 'They are quieter than other rooms', 'They are bigger', 'They are open all night'],
                ],
            ],
            'wap' => [
                ['image' => 'office.svg', 'alt' => 'Colleagues working at computers in an open-plan office.'],
            ],
            'iwr' => [
                ['Describe a person you admire and explain what makes them special.', 'How has this person influenced the choices you have made?'],
                ['Is it better to save money or to spend it on experiences such as travel? Give your opinion.', 'Describe an experience you spent money on that was worth it.'],
            ],
            'wsm' => [
                'Describe the most important invention of the last one hundred years and explain how it has changed daily life.',
            ],
            'sap' => [
                ['image' => 'beach.svg', 'alt' => 'People relaxing and playing on a sunny beach.'],
            ],
            'rts' => [
                'Talk about a book, film or song that is important to you. Explain why it matters.',
                'Describe your ideal weekend. What would you do, and who would you spend it with?',
            ],
            'lts' => [
                [['M', 'Tell me about a place you would like to visit in the future. What would you do there?']],
            ],
            'isp' => [
                [
                    'intro' => 'A health researcher is asking about your daily habits.',
                    'voice' => 'F',
                    'turns' => [
                        'What time do you usually wake up, and why?',
                        'How do you usually get some exercise during the week?',
                        'What does a healthy meal look like for you?',
                        'How much time do you spend looking at screens each day?',
                        'What is one habit you would like to change?',
                        'What advice would you give someone who wants to live more healthily?',
                    ],
                ],
                [
                    'intro' => 'A friend is asking about technology in your life.',
                    'voice' => 'M',
                    'turns' => [
                        'Which piece of technology do you use most every day?',
                        'How does it make your life easier?',
                        'Are there any disadvantages to using it so often?',
                        'What technology do you think older people find most useful?',
                        'How was technology used in your school?',
                        'What new invention would you like to see in the future?',
                    ],
                ],
            ],
            'ssm' => [
                'Should children be allowed to use smartphones? Explain your view with examples.',
            ],
        ];
    }

    /* ── Test 5 ─────────────────────────────────────────────────────── */

    private static function test5(): array
    {
        return [
            'rct' => [
                'Cities around the world are planting more [tr|ees]. Trees provide [sha|de] on hot days, [cle|an] the air and give [bi|rds] a place to [li|ve]. They also make streets more [pleas|ant] for people.',
                'Online shopping has [gro|wn] quickly. It is [conv|enient] because customers can [comp|are] prices at [ho|me], but some people still [pre|fer] to see and [to|uch] products before buying them.',
            ],
            'rsl' => [
                ['word' => 'velvet',  'answer' => 'yes'],
                ['word' => 'frintor', 'answer' => 'no'],
                ['word' => 'harbor',  'answer' => 'yes'],
            ],
            'ir' => [
                [
                    'title'   => 'Test 5 · Interactive Reading 1',
                    'passage' => "Coral reefs cover less than one percent of the ocean floor, but they are home to about a quarter of all marine species. Reefs are built over thousands of years by tiny animals called corals, which form hard skeletons that slowly grow together.\n\nToday, many reefs are in danger. When ocean water becomes too warm, corals lose the colourful algae that live inside them and turn white, a process known as bleaching. Scientists are now testing ways to grow new corals in protected areas and return them to damaged reefs.",
                    'partial' => 'Coral reefs cover less than one percent of the ocean floor, but they are _____.',
                    'sentence_q' => 'Select the best option for the missing part of the sentence.',
                    'sentence'   => ['home to about a quarter of all marine species', 'larger than most continents', 'found only in freezing water', 'made of sand and rock'],
                    'gapped'  => "Coral reefs cover less than one percent of the ocean floor, but they are home to about a quarter of all marine species. Reefs are built over thousands of years by tiny animals called corals, which form hard skeletons that slowly grow together.\n\n_____ When ocean water becomes too warm, corals lose the colourful algae that live inside them and turn white, a process known as bleaching. Scientists are now testing ways to grow new corals in protected areas and return them to damaged reefs.",
                    'gap'     => ['Today, many reefs are in danger.', 'Fishing boats are usually painted white.', 'Most beaches are covered in fine sand.', 'Whales can travel thousands of kilometres.'],
                    'highlight_q' => 'What causes corals to lose their colourful algae?',
                    'highlight'   => 'ocean water becomes too warm',
                    'idea_q'  => 'What is the main idea of the passage?',
                    'idea'    => ['Coral reefs are rich in life but face serious threats.', 'Corals are a type of plant that grows on beaches.', 'Bleaching makes reefs healthier.', 'Scientists have stopped studying coral reefs.'],
                    'titles'  => ['Cities Under the Sea in Danger', 'How to Paint a Boat', 'The Coldest Oceans on Earth', 'Building Sandcastles'],
                ],
                [
                    'title'   => 'Test 5 · Interactive Reading 2',
                    'passage' => "Tea has been enjoyed in China for thousands of years. At first it was used mainly as a medicine, but over time it became an everyday drink. Traders later carried tea along land and sea routes to other parts of Asia and eventually to Europe.\n\nIn Britain, tea was at first an expensive luxury that only the rich could afford. As trade increased and prices fell, it became popular with people of all social classes. Today, it is one of the most widely consumed drinks in the world, second only to water.",
                    'partial' => 'Tea has been enjoyed in China for thousands of years. At first it was used mainly _____.',
                    'sentence_q' => 'Select the best option for the missing part of the sentence.',
                    'sentence'   => ['as a medicine', 'to colour clothing', 'as a type of money', 'to feed farm animals'],
                    'gapped'  => "Tea has been enjoyed in China for thousands of years. At first it was used mainly as a medicine, but over time it became an everyday drink. Traders later carried tea along land and sea routes to other parts of Asia and eventually to Europe.\n\n_____ As trade increased and prices fell, it became popular with people of all social classes. Today, it is one of the most widely consumed drinks in the world, second only to water.",
                    'gap'     => ['In Britain, tea was at first an expensive luxury that only the rich could afford.', 'Coffee grows best in warm, wet climates.', 'Many people drink water with every meal.', 'Ships were once powered by the wind.'],
                    'highlight_q' => 'Why did tea become popular with people of all social classes?',
                    'highlight'   => 'trade increased and prices fell',
                    'idea_q'  => 'What is the main idea of the passage?',
                    'idea'    => ['Tea changed from a rare medicine into a drink enjoyed worldwide.', 'Tea is more popular than water.', 'Only rich people drink tea today.', 'Tea was first grown in Britain.'],
                    'titles'  => ['From Medicine to Everyday Drink', 'The Science of Boiling Water', 'Famous Ships of the Sea', 'How to Grow Vegetables'],
                ],
            ],
            'vic' => [
                'The doctor told him to [|rest] for a few days.',
                'Our team [|won] the match by two goals.',
            ],
            'lsl' => [
                ['word' => 'pencil',  'answer' => 'yes'],
                ['word' => 'framble', 'answer' => 'no'],
                ['word' => 'castle',  'answer' => 'yes'],
            ],
            'lty' => [
                ['Applications for the scholarship close on the first of May.', ['Applications for the scholarship close on the 1st of May.']],
                ['The weather is expected to improve later this week.', []],
                ['Most of the guests arrived shortly after seven.', ['Most of the guests arrived shortly after 7.']],
            ],
            'il' => [
                [
                    'title'    => 'Test 5 · Interactive Listening 1',
                    'scenario' => 'You are talking to a classmate about a missed lecture.',
                    'respond_audio' => [['F', 'You were not in the lecture this morning. Is everything okay?']],
                    'respond'  => ['I was ill, but I am feeling better now.', 'I like playing tennis at weekends.', 'The shop sells fresh fruit.', 'My phone is new.'],
                    'complete_audio' => [
                        ['F', 'The professor talked about the final project. It is due in three weeks.'],
                        ['M', 'Did she say how long it should be?'],
                        ['F', 'About two thousand words, and we can work in pairs. I can share my notes with you tonight.'],
                        ['M', 'That would be really helpful. Thanks!'],
                    ],
                    'complete_q' => 'What will the woman do tonight?',
                    'complete' => ['Share her lecture notes', 'Meet the professor', 'Start writing the project', 'Go to the library'],
                ],
                [
                    'title'    => 'Test 5 · Interactive Listening 2',
                    'scenario' => 'You are calling a hotel to change a booking.',
                    'respond_audio' => [['M', 'Good afternoon, Riverside Hotel. How may I help you?']],
                    'respond'  => ['I would like to change the dates of my booking.', 'The river is very long.', 'I usually go to bed early.', 'My sister is a doctor.'],
                    'complete_audio' => [
                        ['M', 'Your booking is for the twelfth to the fourteenth. What dates would you prefer?'],
                        ['F', 'Could I arrive one day later, on the thirteenth, and stay until the fifteenth?'],
                        ['M', 'Yes, that is possible. There is no extra charge for the change.'],
                        ['F', 'Wonderful, thank you for your help.'],
                    ],
                    'complete_q' => 'What change does the woman make?',
                    'complete' => ['She moves her stay one day later', 'She cancels the booking', 'She adds an extra night', 'She changes to a cheaper room'],
                ],
            ],
            'wap' => [
                ['image' => 'park.svg',  'alt' => 'A family having a picnic in a park beside a tree.'],
                ['image' => 'train.svg', 'alt' => 'Passengers waiting on a platform as a train arrives.'],
            ],
            'iwr' => [
                ['Should public transport be free for everyone? Explain your position.', 'What problems might free public transport create, and how could they be solved?'],
            ],
            'wsm' => [
                'Young people today spend a lot of time on social media. Is this a positive or negative development?',
                'Some people prefer to work for a large company, while others prefer a small business. Which would you choose, and why?',
            ],
            'sap' => [
                ['image' => 'library.svg', 'alt' => 'Students reading and using laptops at a table in a library.'],
            ],
            'rts' => [
                'Talk about a time you helped someone. What happened, and how did you feel afterwards?',
            ],
            'lts' => [
                [['F', 'How do you usually relax after a busy day? Explain why this works for you.']],
                [['M', 'Do you think it is important to learn about history? Why or why not?']],
            ],
            'isp' => [
                [
                    'intro' => 'A club organiser is asking about events in your community.',
                    'voice' => 'F',
                    'turns' => [
                        'What kinds of events take place in your community?',
                        'Which event did you enjoy most recently?',
                        'Who usually organises these events?',
                        'How could more young people be encouraged to take part?',
                        'What new event would you like to start?',
                        'Why do you think community events are important?',
                    ],
                ],
            ],
            'ssm' => [
                'Talk about a tradition in your culture that you think should be preserved. Why is it important?',
                'If you could have dinner with anyone from history, who would it be and what would you ask them?',
            ],
        ];
    }
}
