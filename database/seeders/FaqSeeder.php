<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How do I book a tour or trip?',
                'answer' => 'You can book a tour or trip through our website by browsing our available options, selecting your preferred dates, and completing the booking process. You can also contact our customer service team for assistance.',
                'tag' => 'Booking',
                'active' => true,
            ],
            [
                'question' => 'What is your cancellation policy?',
                'answer' => 'Our cancellation policy varies depending on the type of booking and timing. Generally, cancellations made more than 48 hours before the scheduled departure are eligible for a full refund. Please check the specific terms for your booking.',
                'tag' => 'Cancellation',
                'active' => true,
            ],
            [
                'question' => 'Do you provide transportation to and from the airport?',
                'answer' => 'Yes, we offer airport transfer services for most of our tours and packages. You can add this service during the booking process or contact us to arrange it separately.',
                'tag' => 'Transportation',
                'active' => true,
            ],
            [
                'question' => 'What should I pack for my trip?',
                'answer' => 'Packing requirements depend on your destination and the type of tour. We provide detailed packing lists for each tour, including weather-appropriate clothing, essential documents, and any special equipment needed.',
                'tag' => 'Packing',
                'active' => true,
            ],
            [
                'question' => 'Are meals included in the tour price?',
                'answer' => 'Meal inclusion varies by tour. Some tours include all meals, while others include only breakfast or specific meals. Check the tour details for specific information about what\'s included.',
                'tag' => 'Meals',
                'active' => true,
            ],
            [
                'question' => 'What is the group size for tours?',
                'answer' => 'Group sizes vary depending on the tour type. Small group tours typically have 8-15 participants, while larger tours may have up to 30 people. Private tours are also available for more intimate experiences.',
                'tag' => 'Group Size',
                'active' => true,
            ],
            [
                'question' => 'Do I need travel insurance?',
                'answer' => 'We strongly recommend purchasing travel insurance to protect against unexpected events such as trip cancellations, medical emergencies, or lost luggage. We can help you arrange comprehensive coverage.',
                'tag' => 'Insurance',
                'active' => true,
            ],
            [
                'question' => 'What happens if the weather is bad?',
                'answer' => 'In case of severe weather conditions, we may need to modify or reschedule activities for safety reasons. We will provide alternative arrangements or reschedule options whenever possible.',
                'tag' => 'Weather',
                'active' => true,
            ],
            [
                'question' => 'Are there age restrictions for tours?',
                'answer' => 'Age restrictions vary by tour. Some tours are suitable for all ages, while others may have minimum age requirements due to physical demands or safety considerations. Check individual tour details for specific requirements.',
                'tag' => 'Age Restrictions',
                'active' => true,
            ],
            [
                'question' => 'How do I contact customer support?',
                'answer' => 'You can contact our customer support team through multiple channels: phone, email, or live chat on our website. We\'re available 24/7 to assist with any questions or concerns.',
                'tag' => 'Support',
                'active' => true,
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept various payment methods including credit cards, debit cards, bank transfers, and digital wallets. All payments are processed securely through our encrypted payment system.',
                'tag' => 'Payment',
                'active' => true,
            ],
            [
                'question' => 'Can I customize a tour for my group?',
                'answer' => 'Yes, we offer custom tour packages for groups. Contact our team with your requirements, and we\'ll work with you to create a personalized itinerary that meets your needs and preferences.',
                'tag' => 'Customization',
                'active' => true,
            ],
        ];

        foreach ($faqs as $faqData) {
            $faq = Faq::create([
                'active' => $faqData['active'],
                'tag' => $faqData['tag'],
            ]);

            // Insert translation data
            DB::table('faq_translations')->insert([
                'faq_id' => $faq->id,
                'locale' => 'en',
                'question' => $faqData['question'],
                'answer' => $faqData['answer'],
            ]);
        }

        $this->command->info('FAQs seeded successfully!');
    }
}

