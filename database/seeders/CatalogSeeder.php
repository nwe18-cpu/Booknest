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
    }
}
