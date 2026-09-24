<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('menu_title')->nullable();
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->string('primary_cta_label')->nullable();
            $table->string('secondary_cta_label')->nullable();
            $table->string('image')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        DB::table('services')->insert($this->defaultServices());
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }

    private function defaultServices()
    {
        $now = now();

        return [
            [
                'title' => 'Company Formation in Dubai',
                'menu_title' => 'Company Formation in Dubai',
                'slug' => 'company-formation-in-dubai',
                'excerpt' => 'Set up your Dubai company with the right structure, clear costs and expert support from day one.',
                'primary_cta_label' => 'Get a Dubai setup estimate',
                'secondary_cta_label' => 'Meet the Sharjah Team',
                'content' => $this->dubaiContent(),
                'meta_title' => 'Company Formation Dubai | Business Setup Support',
                'meta_description' => 'Start your company formation Dubai journey with Vertex INT. Get support with Mainland, Free Zone, licences, banking and setup costs.',
                'sort_order' => 1,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Company Formation in UAE',
                'menu_title' => 'Company Formation in UAE',
                'slug' => 'company-formation-in-uae',
                'excerpt' => 'Get your UAE company registered with the right structure and requirements from the first application.',
                'primary_cta_label' => 'Compare setup options',
                'secondary_cta_label' => 'Get a quote',
                'content' => $this->uaeContent(),
                'meta_title' => 'Company Registration UAE | Vertex INT',
                'meta_description' => 'Start your company registration UAE with Vertex INT. Compare setup options, understand requirements and get expert support.',
                'sort_order' => 2,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Company Formation in Sharjah',
                'menu_title' => 'Company Formation in Sharjah',
                'slug' => 'company-formation-in-sharjah',
                'excerpt' => 'Vertex INT supports company formation Sharjah across SEDD Mainland and Sharjah Free Zones. We help local and overseas founders understand the setup requirements, choose a suitable route and move through registration with practical support.',
                'primary_cta_label' => 'Get a Sharjah Quote',
                'secondary_cta_label' => 'Meet the Sharjah Team',
                'content' => $this->sharjahContent(),
                'meta_title' => 'Company Formation Sharjah | Mainland & Free Zone Setup',
                'meta_description' => 'Need help with company formation Sharjah? Get support with Mainland, Free Zone, licences and registration. Speak to our Sharjah team today.',
                'sort_order' => 3,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }

    private function dubaiContent()
    {
        return '<h2>Choose Between Mainland and Free Zone Company Setup</h2><p>Your Dubai Mainland vs Free Zone company choice should depend on how you plan to operate, not which option sounds cheaper. Both routes can offer suitable ownership and setup options depending on the activity.</p><h3>DET / Dubai Mainland Company</h3><p>A Mainland company suits businesses that want to trade across the UAE, work with local customers and build a stronger on-ground presence.</p><ul><li>Trade directly across the UAE</li><li>100% foreign ownership for many activities</li><li>Suitable for local customer-facing businesses</li><li>Physical office options</li><li>Employee and investor visa support</li></ul><h3>Dubai Free Zone Company</h3><p>A Free Zone company works well for online, international and service-based businesses that want a flexible setup.</p><ul><li>100% foreign ownership</li><li>Multiple Free Zones to choose from</li><li>Suitable for online and international businesses</li><li>Flexible workspace options</li><li>Visa packages available</li></ul><h2>Get the Business Activity Right From the Start</h2><p>Your business activity tells the authority what your company is allowed to do. It also affects your legal structure, licence type and whether you need additional approvals.</p><h2>Which Business Licence in Dubai Do You Need?</h2><h3>Commercial Licence</h3><p>Typically used for businesses involved in trading goods and commercial activities.</p><h3>Professional Licence</h3><p>Common for consultants, service providers and businesses built around professional expertise.</p><h3>Industrial Licence</h3><p>Used for manufacturing, production, processing and other approved industrial activities.</p><h2>Documents Required for Company Setup in Dubai</h2><p>Typical documents can include passport copies, photographs, proposed trade names, activity details, UAE visa or Emirates ID where applicable, shareholder and manager details, Memorandum of Association, tenancy documents and external approvals.</p><h2>What Does Company Formation Dubai Cost?</h2><p>The cost can depend on jurisdiction, licence, activities, visas, office or Ejari, approvals, government fees and additional services.</p><h2>How to Register a Company in Dubai</h2><ol><li>Select your business activity.</li><li>Choose Mainland or Free Zone.</li><li>Choose the legal structure.</li><li>Reserve the trade name.</li><li>Obtain initial and external approvals.</li><li>Arrange your business address.</li><li>Submit the incorporation documents.</li><li>Pay the fees and receive the licence.</li></ol><h2>Why Choose Vertex INT?</h2><ul><li>Business-first setup.</li><li>Mainland and Free Zone guidance.</li><li>Clear cost planning.</li><li>Support beyond incorporation.</li></ul>';
    }

    private function uaeContent()
    {
        return '<h2>Who Can Register a Company in the UAE?</h2><p>You do not need to already run a business or live in the UAE to explore company registration. UAE company registration for foreigners is available across many Mainland and Free Zone structures.</p><ul><li>Nationality and residency status</li><li>Proposed business activity</li><li>Number and type of shareholders</li><li>Chosen jurisdiction</li><li>Required legal form</li><li>Additional approvals for regulated activities</li></ul><h2>Compare Mainland and Free Zone Company Registration</h2><h3>UAE Mainland Company</h3><p>A Mainland company can suit businesses that plan to work directly across the UAE, build a local customer base or establish a physical operation.</p><h3>UAE Free Zone Company</h3><p>A Free Zone company can work well for international, online and service-based businesses.</p><h2>Choose a Legal Form That Fits Your Business</h2><p>Your jurisdiction and legal form are two different decisions. Mainland or Free Zone decides where the company is registered. The legal form determines how the business itself is structured.</p><h3>Limited Liability Company (LLC)</h3><p>A common structure for businesses with one or more shareholders.</p><h3>Sole Establishment</h3><p>Often considered by individual founders carrying out eligible activities.</p><h3>Branch of a Foreign Company</h3><p>Can suit an established overseas business that wants to extend operations into the UAE.</p><h2>Documents Required for Company Registration in the UAE</h2><p>Common requirements can include shareholder passports, photographs, proposed trade names, activity details, shareholder and manager information, UAE visa or Emirates ID, corporate documents, premises documents and external approvals.</p><h2>How to Register a Company in the UAE</h2><ol><li>Confirm your business activities.</li><li>Choose Mainland or Free Zone.</li><li>Select the legal form.</li><li>Reserve the trade name.</li><li>Obtain required approvals.</li><li>Prepare and submit documents.</li><li>Arrange business premises where required.</li><li>Pay the registration fees.</li><li>Receive your company licence and documents.</li></ol><h2>Know Your Tax Requirements After Registration</h2><p>Your next steps can include Corporate Tax registration, VAT registration, accounting records and ongoing compliance based on your business.</p>';
    }

    private function sharjahContent()
    {
        return <<<'HTML'
<h2>Choose the Sharjah Route That Fits Your Business</h2><p>Your Sharjah Mainland vs Free Zone company choice affects where you can operate, the type of licence available, workspace requirements, visa options and how easily your business can expand later.</p><h3>SEDD / Sharjah Mainland Company Formation</h3><p>A Mainland setup can suit businesses that want to build a direct presence in the UAE market. Mainland companies in Sharjah are licensed through the Sharjah Economic Development Department (SEDD), with requirements based on the selected activity and legal structure.</p><p>Sharjah Mainland company formation may be a stronger fit if you plan to:</p><ul><li>sell or provide services directly across the UAE</li><li>open a shop, restaurant, clinic or physical service location</li><li>hire employees and build a local team</li><li>use commercial, warehouse or industrial premises</li><li>work with customers outside a specific Free Zone</li></ul><h3>Sharjah Free Zone Company Setup</h3><p>A Sharjah Free Zone company setup can work well for consultants, online businesses, international companies and founders entering a specialised sector. Sharjah has several Free Zones with different activity lists, licence packages, workspace options and visa allowances. This means choosing a Free Zone should be based on what the business needs to do after registration.</p><p>A Free Zone may suit you if you plan to:</p><ul><li>serve mainly international or online customers</li><li>operate a consulting or professional service business</li><li>start with flexible office requirements</li><li>work within a specialised industry</li><li>keep your initial team and visa needs limited</li><li>use a setup designed around a specific business activity</li></ul><p><strong>CTA:</strong> Meet the Sharjah Team</p><h2>Get the Right Business Licence in Sharjah</h2><p>The licence you choose determines the activities your company can legally carry out. It needs to match both your business model and the registration route you select. Some activities may also require additional approval from another authority before the business licence Sharjah application can be completed.</p><h3>Commercial Licence</h3><p>Suitable for qualifying businesses involved in buying, selling, trading and other commercial activities.</p><h3>Professional Licence</h3><p>Used by consultants, specialists and service-based businesses that provide professional expertise.</p><h3>Industrial Licence</h3><p>Used for approved manufacturing, production, processing and other industrial operations.</p><h2>Secure Your Trade Name Before Registration Moves Forward</h2><p>Your trade name becomes part of your legal company identity, so it needs to follow the rules of the relevant Sharjah authority.</p><p>A suitable name should be available for registration and should not include restricted words, misleading references or terms that conflict with UAE naming requirements. It is worth preparing two or three suitable options before starting company registration Sharjah. If your first name is unavailable, you can move to another choice without delaying the next stage.</p><h2>Choose Business Activities That Support Your Plans</h2><p>The activities listed on your licence define what the company is allowed to offer, sell or produce. A business setup Sharjah can cover a wide range of sectors, including:</p><ul><li>trading</li><li>consultancy</li><li>ecommerce</li><li>technology</li><li>marketing and professional services</li><li>media and publishing</li><li>logistics</li><li>manufacturing and industrial activities</li></ul><h2>Documents Required for Sharjah Company Formation</h2><p>The documents required for Sharjah company formation depend on the jurisdiction, activity, shareholder structure and whether the shareholders are individuals or companies.</p><p>Common requirements can include:</p><ul><li>passport copies of shareholders</li><li>passport-size photographs</li><li>proposed trade names</li><li>details of the selected business activities</li><li>UAE visa or Emirates ID where applicable</li><li>shareholder and manager information</li><li>Memorandum of Association where required</li><li>tenancy or premises documents where applicable</li><li>supporting corporate documents for company shareholders</li><li>additional approvals for regulated activities</li></ul><p>For company formation in Sharjah for foreigners, the exact requirements may also depend on residency status, ownership structure and the authority handling the application.</p><h2>What Does Business Setup in Sharjah Cost?</h2><p>The cost of business setup in Sharjah depends on the route and operating requirements of your company. A Free Zone setup with limited workspace and visa needs may have a different cost structure from a Mainland business that requires physical premises, several employees or additional regulatory approvals.</p><p>Your quote may be affected by:</p><ul><li>Mainland or Free Zone registration</li><li>licence type</li><li>selected activities</li><li>visa requirements</li><li>workspace or premises</li><li>authority approvals</li><li>other setup services required</li></ul><p>This is why it is more useful to price the company you actually plan to operate rather than compare only basic licence fees.</p><p><strong>CTA:</strong> Get a Sharjah Quote</p><h2>How Soon Can You Set Up a Company in Sharjah?</h2><p>A straightforward application can move through the registration stages more quickly when the business activity is clear, the trade name is approved and the required documents are ready.</p><p>If you are researching how to register a company in Sharjah, it also helps to separate two different timelines.</p><p><strong>Company registration timeline:</strong> The period needed to complete approvals and receive the licence.</p><p><strong>Operational timeline:</strong> The additional time that may be required for visas, banking, premises and other post-registration steps.</p><p>This gives you a more realistic picture of when the company can actually begin operating.</p><h2>Get Local Support for Your Sharjah Setup</h2><p>Sharjah has its own Mainland authority, Free Zones, licence options and registration procedures. A local setup team can help you work through those requirements without treating Sharjah like a standard UAE package.</p><p>As a company formation consultant Sharjah, Vertex INT can support you with:</p><ul><li>SEDD Mainland registration</li><li>Sharjah Free Zone comparisons</li><li>business activity selection</li><li>licence guidance</li><li>trade name preparation</li><li>company documents</li><li>workspace requirements</li><li>post-registration next steps</li></ul><p>You can discuss the setup with a team that understands the local routes before choosing where and how to register.</p><p><strong>CTA:</strong> Meet the Sharjah Team</p><h2>Get a Clear Route for Your Sharjah Company Setup</h2><p>Not sure which route, licence or activity fits your business? Speak with our team before you commit and get a clearer view of the setup requirements and expected cost.</p><p><strong>CTA:</strong> Meet the Sharjah Team</p><p><strong>CTA:</strong> Get a Sharjah Quote</p><h2>Frequently Asked Questions</h2><h3>Can foreigners start a company in Sharjah?</h3><p>Yes. Foreign investors can fully own many Mainland and Free Zone companies in Sharjah. Certain regulated or strategic activities may have additional ownership or approval requirements.</p><h3>How much does it cost to set up a business in Sharjah?</h3><p>The cost depends on your jurisdiction, licence, activities, visa requirements, workspace and any additional approvals. A tailored quote gives you a clearer figure for the company you actually plan to establish.</p><h3>How do I register a company in Sharjah?</h3><p>The process normally involves selecting Mainland or Free Zone, choosing the business activities, reserving a trade name, preparing the required documents, completing any approvals and applying for the business licence.</p><h3>Should I choose Sharjah Mainland or a Free Zone?</h3><p>Mainland can suit businesses that want wider UAE market access and a stronger physical presence. A Free Zone may suit online, international or specialised businesses. Your customers, workspace and operating plans should guide the choice.</p><h3>What documents are required for company formation Sharjah?</h3><p>Common documents include shareholder passport copies, photographs, trade name options and business activity details. Additional requirements may apply based on the ownership structure, activity and premises.</p><h3>Which business licence do I need in Sharjah?</h3><p>The licence depends on the type of activity your company will carry out. Commercial, professional and industrial licences cover different types of operations.</p><h3>Can foreigners own 100% of a Sharjah company?</h3><p>Full foreign ownership is available for many Mainland activities and is generally available across Sharjah Free Zones. Some activities may follow additional rules.</p><h3>How long does company formation in Sharjah take?</h3><p>The timeline depends on the registration authority, business activity, document readiness and any external approvals required.</p>
HTML;
    }
}
