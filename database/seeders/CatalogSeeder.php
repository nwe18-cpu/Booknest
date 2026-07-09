<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Type;
use App\Models\Item;
use App\Models\Staff;
use App\Models\Author;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = Staff::first() ?? Staff::factory()->create();

        // Create Authors
        $authorMatt = Author::firstOrCreate(['name' => 'Matt Haig'], ['image' => null]);
        $authorJames = Author::firstOrCreate(['name' => 'James Clear'], ['image' => null]);
        $authorRobert = Author::firstOrCreate(['name' => 'Robert C. Martin'], ['image' => null]);
        $authorArthur = Author::firstOrCreate(['name' => 'Arthur Conan Doyle'], ['image' => null]);
        $authorPaulo = Author::firstOrCreate(['name' => 'Paulo Coelho'], ['image' => null]);
        $authorNapoleon = Author::firstOrCreate(['name' => 'Napoleon Hill'], ['image' => null]);
        $authorCal = Author::firstOrCreate(['name' => 'Cal Newport'], ['image' => null]);

        // 1. Create Categories
        $catFiction = Category::firstOrCreate(['name' => 'Fiction & Novels'], [
            'staff_id' => $staff->id,
            'description' => 'Stories from imagination and beyond.',
            'status' => 'active'
        ]);

        $catSelfHelp = Category::firstOrCreate(['name' => 'Self-Development'], [
            'staff_id' => $staff->id,
            'description' => 'Empower your daily routines and mindset.',
            'status' => 'active'
        ]);

        $catTech = Category::firstOrCreate(['name' => 'Science & Tech'], [
            'staff_id' => $staff->id,
            'description' => 'Programming, Science, and Tech Guides.',
            'status' => 'active'
        ]);

        // 2. Create Types (Subcategories)
        $typeNovel = Type::firstOrCreate(['name' => 'Classic & Modern Novels'], [
            'category_id' => $catFiction->id,
            'description' => 'Fictional novels and modern stories.',
            'status' => 'active'
        ]);

        $typeMystery = Type::firstOrCreate(['name' => 'Mystery & Thriller'], [
            'category_id' => $catFiction->id,
            'description' => 'Suspenseful and criminal investigation stories.',
            'status' => 'active'
        ]);

        $typeProductivity = Type::firstOrCreate(['name' => 'Productivity & Habits'], [
            'category_id' => $catSelfHelp->id,
            'description' => 'Build habits and maximize focus.',
            'status' => 'active'
        ]);

        $typeSoftware = Type::firstOrCreate(['name' => 'Software Engineering'], [
            'category_id' => $catTech->id,
            'description' => 'Coding guidelines and architecture.',
            'status' => 'active'
        ]);

        // 3. Create Items (Books with pages_content JSON for the e-Book Reader)
        
        // Book 1: The Midnight Library
        Item::firstOrCreate(['name' => 'The Midnight Library'], [
            'type_id' => $typeNovel->id,
            'author_id' => $authorMatt->id,
            'price' => 12.99,
            'stock_quantity' => 15,
            'description' => 'Nora Seed finds herself in a library between life and death, where she can try every life she could have lived.',
            'pages' => 6,
            'image' => 'midnight_library.jpg',
            'status' => 'active',
            'pages_content' => json_encode([
                [
                    'page' => 1,
                    'title' => 'The Library in the Mist',
                    'content' => "Between life and death there is a library, she said. And within that library, the shelves go on forever. Nora stood still, gazing at the rows of green books fading into the white mist. 'Where am I?' she whispered. Mrs. Elm, the school librarian, smiled from behind the high desk. 'Welcome, Nora. This is the Midnight Library, where you get to see all the lives you could have lived.'"
                ],
                [
                    'page' => 2,
                    'title' => 'The Book of Regrets',
                    'content' => "Mrs. Elm slid a heavy, black leather book across the counter. 'This,' she said, 'is the Book of Regrets. It contains every regret you have ever had, large or small.' Nora opened it. The list was endless. Regretting not marrying Dan, regretting leaving the band, regretting not visiting her father before he passed. Her chest tightened."
                ],
                [
                    'page' => 3,
                    'title' => 'A New Beginning',
                    'content' => "'What if I chose differently?' Nora asked. Mrs. Elm pulled down a dark green book. 'Then let's find out.' As Nora touched the spine, the library dissolved. She found herself in a bustling kitchen. The smell of fresh coffee and pastries filled the air. She was in Paris, married to Dan. But was it really her dream life?"
                ],
                [
                    'page' => 4,
                    'title' => 'The Perfect Imperfection',
                    'content' => "She realized that Paris was lovely, but Dan was still Dan, and she was still Nora. The regrets she thought were blocking her happiness were simply paths she didn't walk. There was no 'perfect' life. In every reality, there was pain, and there was beauty. It was how she faced it that mattered."
                ],
                [
                    'page' => 5,
                    'title' => 'Returning to the Spine',
                    'content' => "The Paris life faded, and she was back in the library. Nora looked at Mrs. Elm. 'I want to live,' she said. 'My own life. The one I left behind. I want to try again.' Mrs. Elm's eyes gleamed with pride. 'Then you must write your own pages, Nora. The clock is striking midnight, and your real story is just beginning.'"
                ],
                [
                    'page' => 6,
                    'title' => 'Epilogue: Nora\'s Choice',
                    'content' => "She woke up gasping for air. The cold floor of her flat was underneath her. She reached for her phone. Nora smiled. The air felt fresh, and the world was filled with endless possibilities. She was alive, and for the first time in a very long time, that was more than enough."
                ]
            ])
        ]);

        // Book 2: Atomic Habits
        Item::firstOrCreate(['name' => 'Atomic Habits'], [
            'type_id' => $typeProductivity->id,
            'author_id' => $authorJames->id,
            'price' => 15.50,
            'stock_quantity' => 25,
            'description' => 'An easy & proven way to build good habits & break bad ones.',
            'pages' => 5,
            'image' => 'atomic_habits.jpg',
            'status' => 'active',
            'pages_content' => json_encode([
                [
                    'page' => 1,
                    'title' => 'The Power of 1%',
                    'content' => "It is so easy to overestimate the importance of one defining moment and underestimate the value of making small improvements on a daily basis. If you can get 1 percent better each day for one year, you'll end up thirty-seven times better by the time you're done. Small shifts lead to massive outcomes."
                ],
                [
                    'page' => 2,
                    'title' => 'Identity-Based Habits',
                    'content' => "Most people start the process of building habits by focusing on *what* they want to achieve (outcomes). The alternative is to build identity-based habits. With this approach, we start by focusing on *who* we wish to become. Your behavior is usually a reflection of your identity. To change, change your self-image."
                ],
                [
                    'page' => 3,
                    'title' => 'The Four Laws of Behavior Change',
                    'content' => "To build a good habit, use the 4 Laws: 1) Make it obvious, 2) Make it attractive, 3) Make it easy, 4) Make it satisfying. If you want to break a bad habit, invert these rules: Make it invisible, make it unattractive, make it difficult, and make it unsatisfying. This simple framework governs human action."
                ],
                [
                    'page' => 4,
                    'title' => 'The Goldilocks Rule',
                    'content' => "Humans experience peak motivation when working on tasks that are right on the edge of their current abilities. Not too hard, not too easy. Just right. This is known as the Goldilocks Rule. When you manage to stay in this flow zone, habits become deeply engaging and almost effortless to sustain over time."
                ],
                [
                    'page' => 5,
                    'title' => 'Conclusion: The Secret to Long-term Success',
                    'content' => "The secret to outstanding results is never stopping. It's not about being perfect, it's about being consistent. A single daily atomic habit might seem small, but combined together, they form a powerful system that propels you toward your goals. Keep compounding your progress day by day."
                ]
            ])
        ]);

        // Book 3: Clean Code
        Item::firstOrCreate(['name' => 'Clean Code'], [
            'type_id' => $typeSoftware->id,
            'author_id' => $authorRobert->id,
            'price' => 29.99,
            'stock_quantity' => 10,
            'description' => 'A handbook of agile software craftsmanship. Learn to write code that reads like well-written prose.',
            'pages' => 6,
            'image' => 'clean_code.jpg',
            'status' => 'active',
            'pages_content' => json_encode([
                [
                    'page' => 1,
                    'title' => 'What is Clean Code?',
                    'content' => "Clean code is simple and direct. It reads like well-written prose. It never obscures the designer's intent, but rather is full of crisp abstractions and straightforward lines of control. Bad code tempts the next developer to make it worse, whereas clean code encourages craftsmanship and care."
                ],
                [
                    'page' => 2,
                    'title' => 'Meaningful Names',
                    'content' => "Names should reveal intent. If a name requires a comment, then the name does not reveal its intent. Choose names that are descriptive and unambiguous. Avoid abbreviations or single-letter variables except for loop counters. Good names act as documentation for your software system."
                ],
                [
                    'page' => 3,
                    'title' => 'Functions: Small & Focused',
                    'content' => "The first rule of functions is that they should be small. The second rule is that *they should be smaller than that*. A function should do one thing, do it well, and do it only. If a function contains nested structures like ifs or loops, it is likely doing too many things and should be refactored."
                ],
                [
                    'page' => 4,
                    'title' => 'Comments: A Necessary Evil?',
                    'content' => "Don't comment bad code—rewrite it. Comments are often used to cover up failure to express ourselves in code. The proper use of comments is to explain decisions that cannot be expressed directly in syntax (like design patterns or performance overrides). Keep comments accurate and minimal."
                ],
                [
                    'page' => 5,
                    'title' => 'The Boy Scout Rule',
                    'content' => "It's not enough to write code well. We must keep it clean over time. The Boy Scout Rule states: *Leave the campground cleaner than you found it.* If we all check in our code slightly cleaner than when we checked it out, the software will not rot. Continuous refactoring is key to long-term health."
                ],
                [
                    'page' => 6,
                    'title' => 'Summary: Agile Craftsmanship',
                    'content' => "Writing clean code is like painting a picture. Knowing how to write code is like knowing how to hold a brush. Clean code requires dedication, practice, and a commitment to professional excellence. Value quality and consistency, and let your software speak of your craftsmanship."
                ]
            ])
        ]);

        // Book 4: Sherlock Holmes
        Item::firstOrCreate(['name' => 'A Study in Scarlet'], [
            'type_id' => $typeMystery->id,
            'author_id' => $authorArthur->id,
            'price' => 9.99,
            'stock_quantity' => 12,
            'description' => 'The legendary detective Sherlock Holmes solves his first case, establishing the science of deduction.',
            'pages' => 6,
            'image' => 'study_in_scarlet.jpg',
            'status' => 'active',
            'pages_content' => json_encode([
                [
                    'page' => 1,
                    'title' => 'Mr. Sherlock Holmes',
                    'content' => "In the year 1878, I took my degree of Doctor of Medicine of the University of London. I had no kinsfolk in England, and was therefore as free as air—or as free as an income of eleven shillings and sixpence a day will permit. I was introduced to a gentleman named Sherlock Holmes, who occupied rooms at 221B Baker Street."
                ],
                [
                    'page' => 2,
                    'title' => 'The Science of Deduction',
                    'content' => "Holmes was a man of singular habits and acute observation. He could tell a man's profession from a simple glance at his hands, sleeves, and boots. 'From a drop of water,' he explained, 'a logician could infer the possibility of an Atlantic or a Niagara. All life is a great chain, the nature of which is known whenever we are shown a single link of it.'"
                ],
                [
                    'page' => 3,
                    'title' => 'The Lauriston Gardens Mystery',
                    'content' => "We were summoned by Inspector Lestrade to an empty house in Lauriston Gardens. On the floor lay the body of a well-dressed man, identified as Enoch Drebber of Cleveland, USA. There was no wound upon his person, but the walls were splattered with blood. Written in red letters upon the wallpaper was the German word 'RACHE'—Revenge."
                ],
                [
                    'page' => 4,
                    'title' => 'The Clue of the Ring',
                    'content' => "Holmes paced the room, examining the floor, the walls, and the body with a magnifying glass. Near the victim, he discovered a small gold wedding ring. 'This is our clue,' he murmured. 'The murderer returned for it, but was disturbed. The mystery is unfolding, Watson, and the science of deduction will soon reveal the killer.'"
                ],
                [
                    'page' => 5,
                    'title' => 'The Science of the Chase',
                    'content' => "By placing a mock advertisement for the lost ring, Holmes lured the killer's accomplice into Baker Street. It was clear the murder was tied to a deep-seated romance and vengeance from the American West. The killer was Jefferson Hope, an American cab driver who sought justice for the death of his beloved Lucy Ferrier."
                ],
                [
                    'page' => 6,
                    'title' => 'Epilogue: Watson\'s Journal',
                    'content' => "Jefferson Hope was captured by Holmes' ingenious trap right inside our rooms. Though the newspapers gave all the credit to Lestrade and Gregson, I knew the truth. I resolved to write down the details of the case, and so Watson's journal first revealed the genius of Sherlock Holmes to the world."
                ]
            ])
        ]);

        // Book 5: The Alchemist
        Item::firstOrCreate(['name' => 'The Alchemist'], [
            'type_id' => $typeNovel->id,
            'author_id' => $authorPaulo->id,
            'price' => 10.99,
            'stock_quantity' => 20,
            'description' => 'A magical story about Santiago, an Andalusian shepherd boy who yearns to travel in search of a worldly treasure.',
            'pages' => 5,
            'image' => null,
            'status' => 'active',
            'pages_content' => json_encode([
                [
                    'page' => 1,
                    'title' => 'Santiago\'s Dream',
                    'content' => "Santiago was a young shepherd boy who loved his sheep and the fields of Andalusia. But lately, he had been having a recurring dream about a treasure hidden near the Egyptian Pyramids. He consulted a gypsy woman, who told him: 'You must go to the Pyramids and find your Personal Legend.'"
                ],
                [
                    'page' => 2,
                    'title' => 'The King of Salem',
                    'content' => "While sitting in a plaza, an old man named Melchizedek, the King of Salem, approached Santiago. 'When you want something, all the universe conspires in helping you to achieve it,' the old man said. He gave Santiago two stones, Urim and Thummim, to help him read the omens, and told him to sell his sheep and follow his dream."
                ],
                [
                    'page' => 3,
                    'title' => 'Crossing the Desert',
                    'content' => "Santiago sold his flock and traveled to Tangier, where he was robbed of all his money. Undeterred, he worked for a crystal merchant for a year, earning enough to join a caravan crossing the Sahara Desert. In the desert, he met an Englishman seeking an alchemist, and Santiago began to learn the language of the world."
                ],
                [
                    'page' => 4,
                    'title' => 'The Oasis and the Alchemist',
                    'content' => "At the Al-Fayoum oasis, Santiago saved the people by reading an omen in the flight of two hawks. There he met Fatima, the love of his life, and the legendary Alchemist himself. The Alchemist agreed to guide him across the dangerous desert. 'Remember that wherever your heart is, there you will find your treasure,' the Alchemist taught him."
                ],
                [
                    'page' => 5,
                    'title' => 'The Pyramids and the Real Treasure',
                    'content' => "Santiago finally reached the Pyramids and began to dig. He was attacked by refugees, who mocked his search. One of them said he also had a dream about a treasure buried under a sycamore tree in Spain. Santiago smiled; he knew his treasure was back home under the tree where he started. He returned and found it, realizing the journey itself had made him wise."
                ]
            ])
        ]);

        // Book 6: Think and Grow Rich
        Item::firstOrCreate(['name' => 'Think and Grow Rich'], [
            'type_id' => $typeProductivity->id,
            'author_id' => $authorNapoleon->id,
            'price' => 14.99,
            'stock_quantity' => 30,
            'description' => 'The landmark bestseller on success and personal achievement, showing the path to wealth and fulfillment.',
            'pages' => 5,
            'image' => null,
            'status' => 'active',
            'pages_content' => json_encode([
                [
                    'page' => 1,
                    'title' => 'Desire: The Starting Point',
                    'content' => "The starting point of all achievement is desire. Weak desire brings weak results, just as a small fire makes a small amount of heat. You must have a burning desire to achieve your goals, backed by a definite plan and persistent execution. Truly desiring something means visualizing and believing in its achievement."
                ],
                [
                    'page' => 2,
                    'title' => 'Faith & Autosuggestion',
                    'content' => "Faith is the head chemist of the mind. When faith is blended with thought, the subconscious mind instantly picks up the vibration and translates it into its spiritual equivalent. Through autosuggestion—the repetition of positive affirmations—you can program your mind to believe in your inevitable success."
                ],
                [
                    'page' => 3,
                    'title' => 'Specialized Knowledge & Imagination',
                    'content' => "General knowledge, no matter how great in quantity, is of little use in accumulating wealth. You must acquire specialized knowledge relative to your definite purpose. Use your imagination to organize this knowledge into plans. The synthetic imagination rearranges old ideas, while the creative imagination creates new ones."
                ],
                [
                    'page' => 4,
                    'title' => 'Decision & Persistence',
                    'content' => "Analysis of hundreds of successful people reveals that every one of them had the habit of reaching decisions promptly and changing them slowly, if at all. Lack of persistence is one of the major causes of failure. Persistence is a state of mind, and it can be cultivated by having a clear purpose and a strong desire."
                ],
                [
                    'page' => 5,
                    'title' => 'The Master Mind',
                    'content' => "No individual can have great power without a Master Mind—the coordination of knowledge and effort in a spirit of harmony between two or more people. When two or more minds cooperate, they create a third, invisible, intangible force which may be likened to a third mind. Surround yourself with people who believe in you."
                ]
            ])
        ]);

        // Book 7: Clean Architecture
        Item::firstOrCreate(['name' => 'Clean Architecture'], [
            'type_id' => $typeSoftware->id,
            'author_id' => $authorRobert->id,
            'price' => 34.99,
            'stock_quantity' => 15,
            'description' => 'A craftsman\'s guide to software structure and design. Learn the principles of architecture from Uncle Bob.',
            'pages' => 5,
            'image' => null,
            'status' => 'active',
            'pages_content' => json_encode([
                [
                    'page' => 1,
                    'title' => 'What is Architecture?',
                    'content' => "The goal of software architecture is to minimize the human resources required to build and maintain the system. The architecture of a software system is the shape given to it by the developers. The shape determines how easy it is to develop, deploy, operate, and maintain the system over time."
                ],
                [
                    'page' => 2,
                    'title' => 'The Dependency Rule',
                    'content' => "The concentric circles of Clean Architecture represent different areas of software. The key rule that makes it work is the Dependency Rule: source code dependencies must point only inwards, toward higher-level policies. Nothing in an inner circle can know anything about something in an outer circle."
                ],
                [
                    'page' => 3,
                    'title' => 'Entities & Use Cases',
                    'content' => "Entities encapsulate enterprise-wide business rules. They can be objects with methods or a set of data structures. Use Cases contain application-specific business rules. They coordinate the flow of data to and from entities, directing them to use their enterprise-wide business rules to achieve the goals of the system."
                ],
                [
                    'page' => 4,
                    'title' => 'Interface Adapters & Frameworks',
                    'content' => "Interface adapters convert data from the format convenient for use cases and entities to the format convenient for external agents like databases or web frameworks. The outermost circle consists of frameworks and tools like databases or web templates. They are details that should be kept separate."
                ],
                [
                    'page' => 5,
                    'title' => 'Summary: Independence & Flexibility',
                    'content' => "By following Clean Architecture, your system becomes: 1) Independent of frameworks, 2) Testable without external elements, 3) Independent of UI, 4) Independent of Database, and 5) Independent of any external agency. This flexibility is essential for creating durable, long-lasting software systems."
                ]
            ])
        ]);

        // Book 8: Deep Work
        Item::firstOrCreate(['name' => 'Deep Work'], [
            'type_id' => $typeProductivity->id,
            'author_id' => $authorCal->id,
            'price' => 16.99,
            'stock_quantity' => 22,
            'description' => 'Rules for focused success in a distracted world. Learn to focus without distraction on cognitively demanding tasks.',
            'pages' => 5,
            'image' => null,
            'status' => 'active',
            'pages_content' => json_encode([
                [
                    'page' => 1,
                    'title' => 'What is Deep Work?',
                    'content' => "Deep work refers to professional activities performed in a state of distraction-free concentration that push your cognitive capabilities to their limit. These efforts create new value, improve your skill, and are hard to replicate. Shallow work, by contrast, is non-cognitively demanding and easy to duplicate."
                ],
                [
                    'page' => 2,
                    'title' => 'The Deep Work Hypothesis',
                    'content' => "The ability to perform deep work is becoming increasingly rare at the exact same time it is becoming increasingly valuable in our economy. As a consequence, the few who cultivate this skill, and then make it the core of their working life, will thrive. High-quality work produced is a function of time spent multiplied by intensity of focus."
                ],
                [
                    'page' => 3,
                    'title' => 'Rule 1: Work Deeply',
                    'content' => "To work deeply, you must build rituals and routines designed to minimize the amount of willpower required to transition into and maintain a state of unbroken focus. Set a schedule, define a clear location, determine how you will support your focus, and establish metrics to track your deep work hours."
                ],
                [
                    'page' => 4,
                    'title' => 'Rule 2: Embrace Boredom',
                    'content' => "To succeed with deep work, you must rewire your brain to handle boredom. If every moment of potential boredom in your life is met with a quick glance at your smartphone, your brain will become conditioned to seek novelty, making it impossible to focus deeply when you actually want to."
                ],
                [
                    'page' => 5,
                    'title' => 'Rule 3: Quit Social Media',
                    'content' => "Social media services are designed to be addictive, fragmenting your attention and diminishing your capacity to concentrate. Evaluate tools based on whether they contribute significantly to your deep goals. Don't use tools just because they offer 'some' benefit. Protect your attention fiercely."
                ]
            ])
        ]);
    }
}
