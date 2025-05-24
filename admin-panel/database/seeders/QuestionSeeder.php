<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Job;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        // Tiffin Master
        $tiffinMasterJob = Job::where('jobType', 'tiffin_master')->first();
        $this->createQuestionsForJob($tiffinMasterJob->id, [
            'What is your primary responsibility as a Tiffin Master?' => 'To prepare and serve hygienic, tasty meals while maintaining quality and safety standards.',
            'How do you ensure food hygiene and safety?' => 'By following cleanliness protocols, using fresh ingredients, wearing proper gear, and maintaining a sanitized kitchen.',
            'What steps do you take to manage meal preparation efficiently?' => 'By planning menus in advance, organizing ingredients, following a preparation schedule, and minimizing waste.',
            'How do you handle customer dietary preferences or allergies?' => 'By carefully noting special requests, avoiding cross-contamination, and preparing customized meals when needed.',
            'How do you manage bulk orders or special occasions?' => 'By planning ahead, organizing additional resources, and preparing meals in batches while maintaining quality.',
            'What would you do if a customer reports a food issue?' => 'Apologize for the inconvenience, investigate the issue, and offer a quick resolution like a replacement or refund.',
            'How do you track inventory and manage ingredients?' => 'By keeping a daily record of stock levels, monitoring ingredient usage, and reordering supplies as required.',
            'What is your approach to handling customer feedback?' => 'By actively listening, addressing concerns promptly, and using feedback to improve service and food quality.',
            'How do you ensure meals are consistently prepared to the same standard?' => 'By following standardized recipes, maintaining portion control, and conducting regular quality checks.',
            'What measures do you take to maintain food presentation?' => 'By packing meals neatly, using high-quality containers, and ensuring portion accuracy for a clean, appealing look.',
            'How do you manage time effectively during busy hours?' => 'By prioritizing tasks, preparing ingredients in advance, and following a clear workflow to maintain efficiency.',
            'How do you stay updated with new recipes or food trends?' => 'By researching regularly, attending food workshops, and experimenting with new recipes to meet customer preferences.',
        ]);

        // Full Tiffin Assistant
        $tiffinAssistantJob = Job::where('jobType', 'tiffin_assistant')->first();
        $this->createQuestionsForJob($tiffinAssistantJob->id, [
            'What is your main role as a Full Tiffin Assistant?' => 'To assist in meal preparation, packaging, and maintaining cleanliness while supporting the Tiffin Master.',
            'How do you ensure food hygiene while handling meals?' => 'By wearing gloves, using sanitized utensils, and following strict cleanliness protocols throughout the process.',
            'What tasks are you responsible for during daily operations?' => 'Assisting with ingredient preparation, packaging meals, cleaning workstations, and maintaining inventory records.',
            'How do you manage time to meet meal preparation deadlines?' => 'By following a daily schedule, preparing ingredients in advance, and working efficiently under supervision.',
            'What would you do if you notice a quality issue with the food?' => 'Immediately inform the Tiffin Master, follow corrective steps, and ensure the issue is resolved before packaging.',
            'How do you handle special meal requests or dietary preferences?' => 'By carefully following instructions, avoiding cross-contamination, and ensuring accuracy while packaging special meals.',
            'What steps do you follow to keep your work area clean?' => 'Regularly cleaning surfaces, sanitizing tools, and maintaining cleanliness during and after food preparation.',
            'How do you assist in managing food inventory?' => 'By tracking ingredient usage, reporting low stock, and helping to organize and store supplies properly.',
            'What would you do if a customer reports an incorrect order?' => 'Notify the team immediately, verify the issue, and assist in correcting the mistake promptly.',
            'How do you ensure consistent meal portioning?' => 'By measuring accurately, following portion guidelines, and double-checking each order before packaging.',
            'What safety measures do you follow while working in the kitchen?' => 'By handling sharp tools carefully, wearing protective gear, and adhering to food safety regulations.',
            'How do you support the Tiffin Master during busy hours?' => 'By staying organized, following instructions promptly, and assisting with meal prep, packaging, and clean-up.',
        ]);

        // TEA Master
        $teaMasterJob = Job::where('jobType', 'tea_master')->first();
        $this->createQuestionsForJob($teaMasterJob->id, [
            'What is your primary responsibility as a TEA Master?' => 'To prepare and serve high-quality tea while maintaining cleanliness and ensuring customer satisfaction.',
            'How do you ensure the quality and taste of the tea?' => 'By using fresh ingredients, following standard recipes, and maintaining consistent brewing times.',
            'What types of tea do you specialize in making?' => 'I specialize in making a variety of teas, including masala chai, green tea, black tea, and other regional Flavors.',
            'How do you manage multiple tea orders during busy hours?' => 'By organizing orders efficiently, preparing in batches, and prioritizing based on urgency.',
            'What steps do you take to maintain hygiene while preparing tea?' => 'By washing utensils regularly, wearing gloves, and keeping the preparation area clean at all times.',
            'How do you handle customer preferences for sweetness or strength?' => 'By asking for their preferences beforehand and adjusting sugar and brewing time accordingly.',
            'What would you do if a customer is dissatisfied with the tea?' => 'Apologize, identify the issue, and promptly prepare a fresh cup to meet their expectations.',
            'How do you manage tea ingredients and inventory?' => 'By tracking daily usage, restocking essentials, and ensuring ingredients are fresh and stored properly.',
            'What precautions do you take while handling hot beverages?' => 'By using heat-resistant tools, handling with care, and ensuring the workspace is safe and organized.',
            'How do you introduce new tea flavors to customers?' => 'By offering small samples, explaining unique ingredients, and suggesting based on their taste preferences.',
            'What measures do you take to ensure consistent taste every time?' => 'By following standardized recipes, using accurate measurements, and maintaining precise brewing methods.',
            'How do you support the team during peak hours?' => 'By preparing ingredients in advance, working efficiently, and coordinating with the team to fulfil orders quickly.',
        ]);

        // Coffee Master
        $coffeeMasterJob = Job::where('jobType', 'coffee_master')->first();
        $this->createQuestionsForJob($coffeeMasterJob->id, [
            'What is your primary responsibility as a Coffee Master?' => 'To prepare and serve high-quality coffee while ensuring customer satisfaction and maintaining cleanliness.',
            'How do you ensure the consistency and quality of each coffee you make?' => 'By following standard recipes, using fresh ingredients, and maintaining precise brewing techniques.',
            'What types of coffee beverages can you prepare?' => 'I can prepare a variety of coffee beverages, including espresso, cappuccino, latte, cold brew, and filter coffee.',
            'How do you manage multiple coffee orders during peak hours?' => 'By prioritizing orders, working efficiently, and preparing beverages in batches while maintaining quality.',
            'What steps do you take to maintain hygiene while preparing coffee?' => 'By cleaning equipment regularly, wearing gloves if needed, and keeping the preparation area sanitized.',
            'How do you customize coffee based on customer preferences?' => 'By adjusting the coffee strength, sweetness, milk options, and other preferences as requested.',
            'What would you do if a customer is unhappy with their coffee?' => 'Apologize sincerely, understand their concern, and remake the coffee to their satisfaction.',
            'How do you ensure that coffee equipment is well-maintained?' => 'By cleaning the machines daily, performing routine checks, and reporting any malfunctions immediately.',
            'What precautions do you take when handling hot coffee?' => 'By using heat-resistant tools, carefully pouring beverages, and ensuring safe handling practices.',
            'How do you introduce new coffee flavors or seasonal beverages to customers?' => 'By offering samples, describing unique flavors, and suggesting options based on customer preferences.',
            'How do you manage coffee inventory and ingredients?' => 'By tracking stock levels, ensuring the freshness of beans and milk, and reordering supplies when needed.',
            'How do you support your team during busy shifts?' => 'By working efficiently, communicating clearly, and assisting with preparation and cleaning tasks.',
        ]);

        // Tea/Coffee Maker
        $teaCoffeeMakerJob = Job::where('jobType', 'tea_coffee_maker')->first();
        $this->createQuestionsForJob($teaCoffeeMakerJob->id, [
            'What is your main responsibility as a Tea/Coffee Maker?' => 'To prepare and serve fresh tea and coffee while maintaining cleanliness and ensuring customer satisfaction.',
            'What types of tea and coffee can you prepare?' => 'I can prepare regular tea, masala tea, green tea, black coffee, milk coffee, and other customized beverages.',
            'How do you ensure the taste and quality of each beverage?' => 'By using fresh ingredients, following recipes accurately, and maintaining proper brewing times.',
            'What steps do you follow to maintain hygiene during preparation?' => 'By washing utensils regularly, sanitizing work areas, and wearing clean gloves if required.',
            'How do you manage multiple beverage orders during busy hours?' => 'By prioritizing orders, preparing in batches, and maintaining a smooth workflow.',
            'What would you do if a customer is not satisfied with their tea or coffee?' => 'Apologize politely, understand the issue, and remake the beverage as per their preference.',
            'How do you keep your equipment clean and functional?' => 'By cleaning after every shift, performing regular maintenance, and reporting issues promptly.',
            'How do you customize drinks according to customer preferences?' => 'By adjusting sugar levels, milk quantity, and flavors as requested by the customer.',
            'What precautions do you take while handling hot beverages?' => 'By using insulated tools, handling carefully, and ensuring safe packaging for takeaway orders.',
            'How do you manage tea and coffee supplies?' => 'By monitoring stock levels, ensuring freshness, and restocking when required.',
            'How do you handle special requests like sugar-free or herbal teas?' => 'By carefully preparing the requested beverage and confirming customer preferences.',
            'How do you contribute to a smooth and efficient kitchen workflow?' => 'By staying organized, coordinating with the team, and maintaining a clean work environment.',
        ]);

        // Tea Stall Helper
        $teaStallHelperJob = Job::where('jobType', 'tea_stall_helper')->first();
        $this->createQuestionsForJob($teaStallHelperJob->id, [
            'What is your main responsibility as a Tea Stall Helper?' => 'To assist in preparing tea, maintain cleanliness, and manage customer orders efficiently.',
            'How do you ensure the cleanliness of the tea stall?' => 'By regularly cleaning utensils, work surfaces, and customer areas while disposing of waste properly.',
            'What tasks do you perform to assist the Tea Master?' => 'I prepare ingredients, serve customers, clean utensils, and manage supplies.',
            'How do you handle customer orders during busy times?' => 'By taking clear orders, prioritizing tasks, and coordinating with the Tea Master for faster service.',
            'What steps do you follow for hygiene and food safety?' => 'I wash hands frequently, wear clean attire, and keep the work area sanitized.',
            'How do you manage inventory and restocking?' => 'By tracking ingredients, reporting low stock, and helping with regular restocking.',
            'What would you do if a customer complains about their order?' => 'I would listen carefully, apologize, and inform the Tea Master to resolve the issue promptly.',
            'How do you handle cash transactions or customer payments?' => 'By collecting the correct amount, giving proper change, and maintaining accuracy.',
            'How do you prepare simple beverages like black tea or lemon tea?' => 'By following basic recipes, boiling water properly, and serving the beverage promptly.',
            'What is your role in maintaining a smooth workflow?' => 'I assist with preparation, keep the area clean, and support the Tea Master as needed.',
            'How do you ensure customer satisfaction?' => 'By serving fresh beverages quickly, maintaining politeness, and responding to requests.',
            'How do you handle special orders like sugar-free tea?' => 'By confirming customer preferences and preparing the beverage accurately.',
        ]);

        // Juice & Smoothie Maker
        $juiceMakerJob = Job::where('jobType', 'juice_smoothie_maker')->first();
        $this->createQuestionsForJob($juiceMakerJob->id, [
            'What are your main responsibilities as a Juice & Smoothie Maker?' => 'To prepare fresh juices and smoothies, maintain cleanliness, and serve customers promptly.',
            'How do you ensure the freshness of ingredients?' => 'By using fresh fruits, storing them properly, and checking for spoilage regularly.',
            'What steps do you follow to prepare a smoothie?' => 'I blend fresh fruits, milk or yogurt, and ice, ensuring the right texture and taste.',
            'How do you handle customer requests for customized drinks?' => 'By listening carefully, confirming their preferences, and preparing the drink accordingly.',
            'What hygiene practices do you follow during preparation?' => 'I wash my hands, clean equipment after each use, and keep the workspace tidy.',
            'How do you manage multiple orders during busy hours?' => 'By organizing tasks, preparing in batches, and prioritizing urgent orders.',
            'What would you do if a customer is unhappy with their drink?' => 'I would apologize, understand the issue, and offer a replacement if needed.',
            'How do you maintain the cleanliness of juicing equipment?' => 'By cleaning and sanitizing machines after each use and performing deep cleaning daily.',
            'How do you handle cash payments and customer transactions?' => 'By accepting payments accurately, providing change, and recording transactions if needed.',
            'What are the key ingredients for a basic fruit juice?' => 'Fresh fruits, filtered water, and optional sugar or natural sweeteners.',
            'How do you ensure efficient inventory management?' => 'By tracking ingredients daily, reporting low stock, and restocking as needed.',
            'What is your approach to serving drinks quickly without compromising quality?' => 'By preparing ingredients in advance and maintaining a fast, organized workflow.',
        ]);

        // Chapati & Rice Maker
        $chapatiRiceMakerJob = Job::where('jobType', 'chapati_rice_maker')->first();
        $this->createQuestionsForJob($chapatiRiceMakerJob->id, [
            'What are your main responsibilities as a Chapati & Rice Maker?' => 'To prepare soft chapatis, cook rice perfectly, maintain hygiene, and serve fresh food.',
            'How do you ensure chapatis are soft and fresh?' => 'By kneading the dough properly, using fresh flour, and cooking on medium heat.',
            'What method do you follow to cook rice perfectly?' => 'I measure the water-to-rice ratio accurately and cook it on low heat until tender.',
            'How do you manage large-scale chapati and rice preparation?' => 'By preparing dough or soaking rice in advance and cooking in batches as needed.',
            'What hygiene practices do you follow during food preparation?' => 'I wash my hands, keep the workspace clean, and use fresh, clean utensils.',
            'How do you handle special dietary requests like less oil or salt?' => 'By understanding the request and adjusting ingredients accordingly while cooking.',
            'What would you do if you run out of dough or rice during a busy shift?' => 'I would quickly prepare more while informing the team to manage orders efficiently.',
            'How do you maintain consistency in taste and texture?' => 'By following fixed measurements and maintaining the same cooking process.',
            'What steps do you take to store leftover dough or cooked rice?' => 'I store them in clean, airtight containers and refrigerate them immediately.',
            'How do you prioritize multiple food orders during rush hours?' => 'By organizing tasks, preparing ingredients beforehand, and cooking systematically.',
            'What techniques do you use to speed up chapati-making without losing quality?' => 'By rolling dough in advance and cooking on multiple pans if required.',
            'How do you ensure food safety and cleanliness in the kitchen?' => 'By wearing gloves, keeping utensils sanitized, and following proper food storage practices.',
        ]);

        // Ice Cream (Counter/Shop)
        $iceCreamJob = Job::where('jobType', 'ice_cream_counter')->first();
        $this->createQuestionsForJob($iceCreamJob->id, [
            'What are your main responsibilities at the ice cream counter/shop?' => 'To serve fresh ice cream, maintain cleanliness, handle customer orders, and manage stock.',
            'How do you ensure the ice cream is stored at the right temperature?' => 'By regularly checking the freezer temperature and ensuring it stays within the required range.',
            'What steps do you follow while serving ice cream to customers?' => 'I use clean scoops, serve accurate portions, and present the order neatly.',
            'How do you manage multiple customer orders during busy hours?' => 'By staying organized, preparing common flavors in advance, and handling orders one by one efficiently.',
            'How do you keep the ice cream counter clean and hygienic?' => 'By wiping surfaces regularly, cleaning scoops after each use, and following hygiene protocols.',
            'What do you do if a customer requests a custom flavor combination?' => 'I listen carefully, confirm the request, and prepare the combination as desired.',
            'How do you handle a situation where a customer receives the wrong order?' => 'I apologize, quickly replace the order, and ensure customer satisfaction.',
            'What steps do you take to manage and track ice cream inventory?' => 'I monitor stock levels daily, note low supplies, and report for restocking as needed.',
            'How do you deal with melted or spoiled ice cream?' => 'I immediately discard it and ensure the freezer is functioning correctly to prevent spoilage.',
            'How do you assist customers in choosing flavors?' => 'By describing popular flavors, offering samples (if allowed), and suggesting combinations.',
            'What actions do you take when handling cash or digital payments?' => 'I process payments carefully, give correct change, and maintain accurate records.',
            'How do you manage customer complaints about taste or quality?' => 'I listen patiently, apologize if necessary, and resolve the issue by offering a replacement or refund.',
        ]);

        // Bakery Assistant
        $bakeryAssistantJob = Job::where('jobType', 'bakery_assistant')->first();
        $this->createQuestionsForJob($bakeryAssistantJob->id, [
            'What are your main responsibilities as a bakery assistant?' => 'Assisting in preparing baked goods, maintaining cleanliness, managing inventory, and serving customers.',
            'How do you ensure the freshness of bakery items?' => 'By following proper storage methods, labelling items with dates, and regularly checking for freshness.',
            'What steps do you follow when packaging baked goods?' => 'I use clean packaging, handle items gently, and label them correctly for easy identification.',
            'How do you manage customer orders during busy hours?' => 'By staying organized, prioritizing orders, and working efficiently while maintaining quality.',
            'What hygiene practices do you follow while handling bakery products?' => 'I wear gloves, wash hands frequently, and sanitize surfaces regularly.',
            'How do you handle a situation where a customer receives the wrong order?' => 'I apologize, quickly correct the mistake, and ensure the customer is satisfied.',
            'What steps do you take to manage bakery inventory?' => 'I track stock levels, note items that need restocking, and report to the manager as needed.',
            'How do you assist customers in choosing bakery items?' => 'I describe product ingredients, suggest popular items, and answer customer queries politely.',
            'What do you do if you notice spoiled or expired bakery items?' => 'I immediately remove them, inform the supervisor, and follow disposal guidelines.',
            'How do you ensure accurate billing and payment handling?' => 'I double-check orders, handle cash or digital payments carefully, and maintain accurate records.',
            'What do you do if a customer has dietary restrictions or allergies?' => 'I inform them about ingredients and suggest suitable alternatives if available.',
            'How do you manage cleaning duties in the bakery area?' => 'I clean surfaces regularly, sanitize equipment, and follow bakery hygiene standards.',
        ]);

        // Home Chef
        $homeChefJob = Job::where('jobType', 'home_chef')->first();
        $this->createQuestionsForJob($homeChefJob->id, [
            'What are your main responsibilities as a home chef?' => 'Preparing meals, following special recipes, maintaining kitchen cleanliness, and ensuring food quality.',
            'How do you handle special recipe requests?' => 'I follow the recipe carefully, use fresh ingredients, and ensure the taste matches expectations.',
            'What steps do you take to maintain hygiene while cooking?' => 'I wash hands regularly, sanitize utensils, and keep the kitchen clean during and after cooking.',
            'How do you manage multiple dish preparations simultaneously?' => 'I plan the cooking process, prioritize dishes, and manage time efficiently without compromising quality.',
            'What do you do if a client requests a dish, you\'re unfamiliar with?' => 'I research the recipe, practice it if needed, and ensure it meets the client\'s preferences.',
            'How do you ensure food is prepared on time?' => 'I follow a cooking schedule, prepare ingredients in advance, and work efficiently.',
            'What do you do if a client has dietary restrictions?' => 'I adjust the recipe accordingly, avoid restricted ingredients, and confirm with the client.',
            'How do you manage grocery shopping for special recipes?' => 'I prepare a list, select quality ingredients, and ensure everything needed is available.',
            'What steps do you take to prevent food wastage?' => 'I measure ingredients accurately, store leftovers properly, and reuse suitable items.',
            'How do you respond if a client is unsatisfied with a dish?' => 'I listen to their feedback, make adjustments, and strive to improve the next time.',
            'How do you maintain a clean and organized kitchen?' => 'I clean as I cook, organize utensils properly, and sanitize surfaces regularly.',
            'What is your approach to preparing meals for special occasions?' => 'I plan the menu, prepare in advance, and focus on presentation and taste for a memorable experience.',
        ]);
    }

    private function createQuestionsForJob($jobId, $questionsWithAnswers)
    {
        foreach ($questionsWithAnswers as $question => $answer) {
            Question::create([
                'text' => $question,
                'body' => $answer,
                'jobType_Id' => $jobId,
                'experience_level' => 'intermediate', // Default experience level, you might want to adjust this
            ]);
        }
    }
}