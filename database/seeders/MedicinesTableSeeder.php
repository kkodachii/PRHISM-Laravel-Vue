<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MedicinesTableSeeder extends Seeder
{
    public function run()
    {
        $userId = 1;
        $batchId = 1;
        $barangays = [1, 2, 3];
        $providers = ['LGO', 'DOH', 'Donation'];
        $quantityRange = [10, 100];

        // Retrieve all generic names with their corresponding category_id
        $genericNames = DB::table('generic_names')->get();

        // Define brands associated with each generic name
        $brands = [
            'Amlodipine' => [
                'Norvasc 20mg, 30 tablets per pack',
                'Amlodac 10mg, 30 tablets per pack',
                'Amlodip 5mg, 30 tablets per pack'
            ],
            'Losartan' => [
                'Cozaar 50mg, 30 tablets per pack',
                'Losartan-HCTZ 50mg/12.5mg, 30 tablets per pack',
                'Hyzaar 100mg/12.5mg, 30 tablets per pack'
            ],
            'Metformin' => [
                'Glucophage 500mg, 30 tablets per pack',
                'Fortamet 500mg, 30 tablets per pack',
                'Riomet 100mg/5ml, 120ml per bottle'
            ],
            'Atorvastatin' => [
                'Lipitor 10mg, 30 tablets per pack',
                'Atorva 20mg, 30 tablets per pack',
                'Taurvac 40mg, 30 tablets per pack'
            ],
            'Simvastatin' => [
                'Zocor 10mg, 30 tablets per pack',
                'Simvacor 20mg, 30 tablets per pack',
                'SimvaStat 40mg, 30 tablets per pack'
            ],
            'Insulin Glargine' => [
                'Lantus 100 units/ml, 10ml vial',
                'Basaglar 100 units/ml, 10ml vial',
                'Toujeo 300 units/ml, 1.5ml pre-filled pen'
            ],
            'Insulin Aspart' => [
                'Novolog 100 units/ml, 10ml vial',
                'Fiasp 100 units/ml, 10ml vial',
                'Insulin Aspart FlexPen 100 units/ml, 3ml pre-filled pen'
            ],
            'Gliclazide' => [
                'Diamicron 80mg, 30 tablets per pack',
                'Glyburide 2.5mg, 30 tablets per pack',
                'Gliclazide MR 60mg, 30 tablets per pack'
            ],
            'Glipizide' => [
                'Glucotrol 5mg, 30 tablets per pack',
                'Glucotrol XL 10mg, 30 tablets per pack',
                'Glynase 5mg, 30 tablets per pack'
            ],
            'Diphtheria Vaccine' => [
                'Daptacel 0.5ml per dose, 10 doses per vial',
                'Infanrix 0.5ml per dose, 10 doses per vial',
                'Pentacel 0.5ml per dose, 10 doses per vial'
            ],
            'Measles Vaccine' => [
                'M-M-R II 0.5ml per dose, 10 doses per vial',
                'Priorix 0.5ml per dose, 10 doses per vial',
                'Attenuvax 0.5ml per dose, 10 doses per vial'
            ],
            'Hepatitis B Vaccine' => [
                'Engerix-B 20mcg, 1ml vial',
                'Recombivax HB 10mcg, 1ml vial',
                'Heplisav-B 0.5ml, 1 dose pre-filled syringe'
            ],
            'Polio Vaccine' => [
                'IPOL 0.5ml per dose, 10 doses per vial',
                'Pentacel 0.5ml per dose, 10 doses per vial',
                'Vaxchora 1 dose, 1 vial'
            ],
            'COVID-19 Vaccine' => [
                'Pfizer-BioNTech 0.3ml per dose, 10 doses per vial',
                'Moderna 0.5ml per dose, 10 doses per vial',
                'Janssen 0.5ml per dose, 5 doses per vial'
            ],
            'Ethinyl Estradiol' => [
                'Estinyl 0.05mg, 30 tablets per pack',
                'Estrace 1mg, 30 tablets per pack',
                'Femtrace 0.9mg, 30 tablets per pack'
            ],
            'Levonorgestrel' => [
                'Plan B 1.5mg, 1 tablet per pack',
                'Next Choice 1.5mg, 1 tablet per pack',
                'Mirena 52mg, 1 device per pack (IUD)'
            ],
            'Medroxyprogesterone' => [
                'Depo-Provera 150mg/ml, 1ml vial',
                'Provera 10mg, 30 tablets per pack',
                'Depo-subQ Provera 104mg/0.65ml, 1 syringe per pack'
            ],
            'Norethindrone' => [
                'Aygestin 5mg, 30 tablets per pack',
                'Micronor 0.35mg, 30 tablets per pack',
                'Nor-QD 0.35mg, 30 tablets per pack'
            ],
            'Etonogestrel Implant' => [
                'Nexplanon 68mg, 1 implant per pack',
                'Implanon 68mg, 1 implant per pack',
                'Etonogestrel 68mg, 1 implant per pack'
            ],
            'Isoniazid' => [
                'Nydrazid 300mg, 30 tablets per pack',
                'Isotamine 100mg, 30 tablets per pack',
                'Isoniazid 300mg, 30 tablets per pack'
            ],
            'Rifampicin' => [
                'Rifadin 300mg, 30 capsules per pack',
                'Rifampin 300mg, 30 capsules per pack',
                'Rifater 150mg/75mg/300mg, 30 tablets per pack'
            ],
            'Vitamin A' => [
                'Aquasol A 2500 IU, 10ml per bottle',
                'Retinol 5000 IU, 10ml per bottle',
                'Beta-Carotene 25,000 IU, 30 capsules per bottle'
            ],
            'Vitamin D' => [
                'Calcitriol 0.25mcg, 30 capsules per bottle',
                'Cholecalciferol 1000 IU, 30 tablets per bottle',
                'Ergocalciferol 50,000 IU, 10 capsules per bottle'
            ],
            'Folic Acid' => [
                'Folate 1mg, 30 tablets per bottle',
                'Folvite 0.4mg, 30 tablets per bottle',
                'Vitamin B9 400mcg, 30 tablets per bottle'
            ],
            'Iron Sulfate' => [
                'Ferrous Sulfate 325mg, 30 tablets per bottle',
                'Feosol 325mg, 30 tablets per bottle',
                'Slow Fe 45mg, 30 tablets per bottle'
            ],
            'Calcium' => [
                'Caltrate 600mg, 30 tablets per bottle',
                'Citracal 500mg, 30 tablets per bottle',
                'Os-Cal 500mg, 30 tablets per bottle'
            ],
            'Oral Rehydration Salts' => [
                'Pedialyte 500ml, 1 bottle per pack',
                'Rehydrate 500ml, 1 bottle per pack',
                'Electrolyte 500ml, 1 bottle per pack'
            ],
            'Zinc Sulfate' => [
                'Zinc-5 220mg, 30 tablets per pack',
                'Zinc Sulfate 220mg, 30 tablets per pack',
                'Zincovit 20mg, 30 tablets per pack'
            ],
            'Cotrimoxazole' => [
                'Bactrim 400mg/80mg, 20 tablets per pack',
                'Septra 400mg/80mg, 20 tablets per pack',
                'Cotrim 400mg/80mg, 20 tablets per pack'
            ],
            'Amoxicillin' => [
                'Amoxil 500mg, 30 capsules per pack',
                'Trimox 500mg, 30 capsules per pack',
                'Moxatag 775mg, 30 tablets per pack'
            ],
            'Azithromycin' => [
                'Zithromax 250mg, 6 tablets per pack',
                'Z-Pak 250mg, 6 tablets per pack',
                'Azithromycin 500mg, 30 tablets per pack'
            ],
            'Ciprofloxacin' => [
                'Cipro 500mg, 30 tablets per pack',
                'Cipro XR 1000mg, 30 tablets per pack',
                'Ciprofloxacin 500mg, 30 tablets per pack'
            ],
            'Doxycycline' => [
                'Vibramycin 100mg, 30 capsules per pack',
                'Doryx 100mg, 30 tablets per pack',
                'Monodox 100mg, 30 capsules per pack'
            ],
            'Fluconazole' => [
                'Diflucan 150mg, 1 tablet per pack',
                'Fluconazole 150mg, 1 tablet per pack',
                'Trican 150mg, 1 tablet per pack'
            ],
            'Fluoxetine' => [
                'Prozac 20mg, 30 capsules per pack',
                'Sarafem 10mg, 30 tablets per pack',
                'Selfemra 10mg, 30 tablets per pack'
            ],
            'Sertraline' => [
                'Zoloft 50mg, 30 tablets per pack',
                'Lustral 50mg, 30 tablets per pack',
                'Sertraline 50mg, 30 tablets per pack'
            ],
            'Olanzapine' => [
                'Zyprexa 10mg, 30 tablets per pack',
                'Symbyax 6mg/25mg, 30 capsules per pack',
                'Olanzapine 10mg, 30 tablets per pack'
            ],
            'Risperidone' => [
                'Risperdal 2mg, 30 tablets per pack',
                'Risperdal Consta 25mg, 1 vial per pack',
                'Risperidone 2mg, 30 tablets per pack'
            ],
            'Diazepam' => [
                'Valium 5mg, 30 tablets per pack',
                'Diazepam 5mg, 30 tablets per pack',
                'Valrelease 5mg, 30 tablets per pack'
            ],
            'Paracetamol' => [
                'Biogesic 500mg, 20 tablets per pack',
                'Panadol 500mg, 20 tablets per pack',
                'Acetaminophen 500mg, 20 tablets per pack'
            ],
            'Ibuprofen' => [
                'Advil 200mg, 30 tablets per pack',
                'Motrin 200mg, 30 tablets per pack',
                'Nurofen 200mg, 30 tablets per pack'
            ],
            'Diclofenac' => [
                'Voltaren 25mg, 20 tablets per pack',
                'Cataflam 25mg, 20 tablets per pack',
                'Dicloflam 25mg, 20 tablets per pack'
            ],
            'Naproxen' => [
                'Aleve 220mg, 20 tablets per pack',
                'Naprosyn 250mg, 20 tablets per pack',
                'Naprelan 375mg, 20 tablets per pack'
            ],
            'Tramadol' => [
                'Ultram 50mg, 30 tablets per pack',
                'ConZip 50mg, 30 tablets per pack',
                'Rybix 50mg, 30 tablets per pack'
            ],
            'Aspirin' => [
                'Bayer Aspirin 325mg, 30 tablets per pack',
                'Ecotrin 325mg, 30 tablets per pack',
                'Bufferin 325mg, 30 tablets per pack'
            ],
            'Clopidogrel' => [
                'Plavix 75mg, 30 tablets per pack',
                'Clopidogrel 75mg, 30 tablets per pack',
                'Clopidogrel Bisulfate 75mg, 30 tablets per pack'
            ],
            'Bisoprolol' => [
                'Zebeta 5mg, 30 tablets per pack',
                'Bisolcard 5mg, 30 tablets per pack',
                'Emcor 5mg, 30 tablets per pack'
            ],
            'Enalapril' => [
                'Vasotec 10mg, 30 tablets per pack',
                'Epaned 5mg, 30 tablets per pack',
                'Enalapril Maleate 5mg, 30 tablets per pack'
            ],
            'Hydrochlorothiazide' => [
                'Hydrodiuril 25mg, 30 tablets per pack',
                'HCTZ 25mg, 30 tablets per pack',
                'Microzide 25mg, 30 tablets per pack'
            ],
            'Salbutamol' => [
                'Ventolin 100mcg, 200 inhalations per canister',
                'Proventil 90mcg, 200 inhalations per canister',
                'Salbutamol Sulfate 100mcg, 200 inhalations per canister'
            ],
            'Budesonide' => [
                'Pulmicort 90mcg, 200 inhalations per canister',
                'Entocort 3mg, 30 capsules per pack',
                'Symbicort 160mcg/4.5mcg, 120 inhalations per canister'
            ],
            'Montelukast' => [
                'Singulair 10mg, 30 tablets per pack',
                'Montelukast 10mg, 30 tablets per pack',
                'Montelukast Sodium 10mg, 30 tablets per pack'
            ],
            'Fluticasone' => [
                'Flonase 50mcg, 120 sprays per bottle',
                'Advair 250mcg/50mcg, 120 inhalations per inhaler',
                'Flovent 110mcg, 120 inhalations per inhaler'
            ],
            'Theophylline' => [
                'Theo-Dur 100mg, 30 tablets per pack',
                'Elixophyllin 100mg, 30 tablets per pack',
                'Theochron 100mg, 30 tablets per pack'
            ],
            'Omeprazole' => [
                'Prilosec 20mg, 30 tablets per pack',
                'Losec 20mg, 30 tablets per pack',
                'Omeprazole 20mg, 30 capsules per pack'
            ],
            'Ranitidine' => [
                'Zantac 150mg, 30 tablets per pack',
                'Rani-D 150mg, 30 tablets per pack',
                'Ranidase 150mg, 30 tablets per pack'
            ],
            'Loperamide' => [
                'Imodium 2mg, 30 capsules per pack',
                'Loperamide 2mg, 30 capsules per pack',
                'Loperamide HCl 2mg, 30 capsules per pack'
            ],

            'Domperidone' => [
                'Motilium 10mg, 30 tablets per pack',
                'Domperidone 10mg, 30 tablets per pack',
                'Domperidone Maleate 10mg, 30 tablets per pack'
            ],
            'Pantoprazole' => [
                'Protonix 40mg, 30 tablets per pack',
                'Pantoprazole 40mg, 30 tablets per pack',
                'Pantoloc 40mg, 30 tablets per pack'
            ],
            'Levothyroxine' => [
                'Synthroid 100mcg, 30 tablets per pack',
                'Levoxyl 100mcg, 30 tablets per pack',
                'Euthyrox 100mcg, 30 tablets per pack'
            ],
            'Prednisone' => [
                'Deltasone 5mg, 30 tablets per pack',
                'Sterapred 5mg, 30 tablets per pack',
                'Prednisone 5mg, 30 tablets per pack'
            ],
            'Testosterone' => [
                'AndroGel 1%, 1.25g per pump, 30 pumps per pack',
                'Testim 1%, 1.25g per tube, 30 tubes per pack',
                'Axiron 30mg/actuation, 30 applications per pack'
            ],
            'Progesterone' => [
                'Prometrium 100mg, 30 capsules per pack',
                'Endometrin 100mg, 30 tablets per pack',
                'Progesterone 100mg, 30 capsules per pack'
            ],
            'Phenytoin' => [
                'Dilantin 100mg, 30 capsules per pack',
                'Phenytek 100mg, 30 capsules per pack',
                'Phenytoin Sodium 100mg, 30 tablets per pack'
            ],
            'Carbamazepine' => [
                'Tegretol 200mg, 30 tablets per pack',
                'Carbatrol 200mg, 30 capsules per pack',
                'Equetro 200mg, 30 tablets per pack'
            ],
            'Levetiracetam' => [
                'Keppra 500mg, 30 tablets per pack',
                'Elepsia 500mg, 30 tablets per pack',
                'Levetiracetam 500mg, 30 tablets per pack'
            ],
            'Valproic Acid' => [
                'Depakote 250mg, 30 tablets per pack',
                'Depakene 250mg, 30 capsules per pack',
                'Valproic Acid 250mg, 30 tablets per pack'
            ],
            'Gabapentin' => [
                'Neurontin 300mg, 30 capsules per pack',
                'Gralise 300mg, 30 tablets per pack',
                'Horizant 600mg, 30 tablets per pack'
            ],
            'Clotrimazole' => [
                'Lotrimin 1%, 30g tube per pack',
                'Mycelex 1%, 30g tube per pack',
                'Clotrimazole 1%, 30g tube per pack'
            ],
            'Hydrocortisone' => [
                'Hydrocortisone 10mg, 30 tablets per pack',
                'Cortaid 1%, 30g tube per pack',
                'Hytone 1%, 30g tube per pack'
            ],
            'Benzoyl Peroxide' => [
                'Proactiv 2.5%, 30g tube per pack',
                'PanOxyl 10%, 30g tube per pack',
                'Benzac 5%, 30g tube per pack'
            ],
            'Salicylic Acid' => [
                'Compound W 17%, 11g tube per pack',
                'Salicylic Acid 2%, 30g tube per pack',
                'Neutrogena 2%, 30g bottle per pack'
            ],
            'Mupirocin' => [
                'Bactroban 2%, 22g tube per pack',
                'Centany 2%, 22g tube per pack',
                'Mupirocin 2%, 22g tube per pack'
            ],
            'Methotrexate' => [
                'Trexall 2.5mg, 30 tablets per pack',
                'Otrexup 25mg/ml, 1 syringe per pack',
                'Rheumatrex 2.5mg, 30 tablets per pack'
            ],
            'Cyclophosphamide' => [
                'Cytoxan 50mg, 30 tablets per pack',
                'Neosar 50mg, 30 tablets per pack',
                'Procytox 50mg, 30 tablets per pack'
            ],
            'Doxorubicin' => [
                'Adriamycin 50mg, 1 vial per pack',
                'Doxil 50mg, 1 vial per pack',
                'Caelyx 50mg, 1 vial per pack'
            ],
            'Tamoxifen' => [
                'Nolvadex 20mg, 30 tablets per pack',
                'Tamoxifen 20mg, 30 tablets per pack',
                'Tamofen 20mg, 30 tablets per pack'
            ],
            'Cisplatin' => [
                'Platinol 50mg, 1 vial per pack',
                'Cisplatin 50mg, 1 vial per pack',
                'Cisplatin Injection 50mg, 1 vial per pack'
            ],
            'Fluoride' => [
                'Fluoridex 1.1%, 120g tube per pack',
                'Prevident 1.1%, 120g tube per pack',
                'Act Fluoride 0.05%, 473ml bottle per pack'
            ],
            'Chlorhexidine' => [
                'Peridex 0.12%, 473ml bottle per pack',
                'PerioGard 0.12%, 473ml bottle per pack',
                'Chlorhexidine Gluconate 0.12%, 473ml bottle per pack'
            ],
            'Clindamycin' => [
                'Cleocin 300mg, 30 capsules per pack',
                'Clindagel 1%, 30g tube per pack',
                'Clindamycin Phosphate 1%, 30g tube per pack'
            ],
            'Metronidazole' => [
                'Flagyl 500mg, 30 tablets per pack',
                'MetroGel 0.75%, 30g tube per pack',
                'Metronidazole Topical 0.75%, 30g tube per pack'
            ],
            'Timolol' => [
                'Timoptic 0.25%, 5ml bottle per pack',
                'Betimol 0.25%, 5ml bottle per pack',
                'Timolol Maleate 0.25%, 5ml bottle per pack'
            ],
            'Ofloxacin' => [
                'Floxin 200mg, 10 tablets per pack',
                'Ocuflox 0.3%, 5ml bottle per pack',
                'Ofloxacin Otic 0.3%, 10ml bottle per pack'
            ],
            'Neomycin' => [
                'Neosporin 0.5%, 20g tube per pack',
                'Mycitracin 0.5%, 20g tube per pack',
                'Neomycin Poly 0.5%, 20g tube per pack'
            ],
            'Betaxolol' => [
                'Betoptic 0.25%, 5ml bottle per pack',
                'Betoptic S 0.25%, 5ml bottle per pack',
                'Betaxolol Hydrochloride 0.25%, 5ml bottle per pack'
            ],
            'Brimonidine' => [
                'Alphagan 0.2%, 5ml bottle per pack',
                'Lumify 0.025%, 5ml bottle per pack',
                'Brimonidine Tartrate 0.2%, 5ml bottle per pack'
            ],
            'Clomiphene' => [
                'Clomid 50mg, 30 tablets per pack',
                'Serophene 50mg, 30 tablets per pack',
                'Clomiphene Citrate 50mg, 30 tablets per pack'
            ],
            'Human Chorionic Gonadotropin' => [
                'Ovidrel 250mcg, 1 syringe per pack',
                'Profasi 5000 IU, 1 vial per pack',
                'Pregnyl 5000 IU, 1 vial per pack'
            ],
            'Misoprostol' => [
                'Cytotec 200mcg, 30 tablets per pack',
                'Misoprostol 200mcg, 30 tablets per pack',
                'Arthrotec 50mg/200mcg, 30 tablets per pack'
            ],
            'Mifepristone' => [
                'Mifeprex 200mg, 1 tablet per pack',
                'Korlym 300mg, 30 tablets per pack',
                'Mifepristone 200mg, 1 tablet per pack'
            ],
            'Warfarin' => [
                'Coumadin 5mg, 30 tablets per pack',
                'Jantoven 5mg, 30 tablets per pack',
                'Warfarin Sodium 5mg, 30 tablets per pack'
            ],
            'Heparin' => [
                'Heparin Sodium 5000 IU/ml, 1 vial per pack',
                'HepFlush 10 U/ml, 10 vials per pack',
                'Heparin Lock 10 U/ml, 10 vials per pack'
            ],
            'Ferrous Sulfate' => [
                'Fer-In-Sol 220mg, 30 tablets per pack',
                'Slow Fe 120mg, 30 tablets per pack',
                'Ferro-Sequels 325mg, 30 tablets per pack'
            ],
            'Darbepoetin' => [
                'Aranesp 30mcg/0.3ml, 1 syringe per pack',
                'Darbepoetin Alfa 40mcg/0.4ml, 1 syringe per pack',
                'Aranesp SureClick 30mcg/0.3ml, 1 syringe per pack'
            ],
            'Epoetin Alfa' => [
                'Epogen 2000 IU/ml, 1 vial per pack',
                'Procrit 2000 IU/ml, 1 vial per pack',
                'Epoetin Alfa 2000 IU/ml, 1 vial per pack'
            ],
            'Epinephrine' => [
                'Adrenalin 1mg/ml, 1 vial per pack',
                'EpiPen 0.3mg, 2 auto-injectors per pack',
                'Epipen Auto-Injector 0.3mg, 2 auto-injectors per pack'
            ],
            'Naloxone' => [
                'Narcan 4mg, 1 nasal spray per pack',
                'Evzio 0.4mg, 1 auto-injector per pack',
                'Naloxone Hydrochloride 0.4mg, 1 vial per pack'
            ],
            'Dopamine' => [
                'Intropin 40mg/20ml, 1 vial per pack',
                'Dopastat 40mg/20ml, 1 vial per pack',
                'Dopamine Hydrochloride 40mg/20ml, 1 vial per pack'
            ],
            'Atropine' => [
                'Atropine Sulfate 0.5mg/1ml, 1 vial per pack',
                'Atropine Injection 0.5mg/1ml, 1 vial per pack',
                'Atropine 1mg, 1 vial per pack'
            ],
            'Amiodarone' => [
                'Cordarone 200mg, 30 tablets per pack',
                'Pacerone 200mg, 30 tablets per pack',
                'Amiodarone 200mg, 30 tablets per pack'
            ],
            'Pyrazinamide' => [
                'PZA 500mg, 30 tablets per pack',
                'Tebrazid 500mg, 30 tablets per pack',
                'Zyvox 500mg, 30 tablets per pack'
            ],
            'Ethambutol' => [
                'Myambutol 100mg, 30 tablets per pack',
                'Etibi 100mg, 30 tablets per pack',
                'Ethambutol 100mg, 30 tablets per pack'
            ],
            'Streptomycin' => [
                'Streptomycin 1g, 10 vials per pack',
                'Streptomycin Sulfate 1g, 10 vials per pack',
                'Aminosidine 1g, 10 vials per pack'
            ]

        ];

        foreach ($barangays as $barangayId) {
            for ($i = 0; $i < 20; $i++) {
                // Randomly select a generic name
                $genericName = $genericNames->random();

                // Select a brand name string associated with the generic name, or use a placeholder if none are specified
                $brandName = $brands[$genericName->generic_name][array_rand($brands[$genericName->generic_name] ?? ['Generic Brand'])];
                // Generate quantity and expiration date
                $quantity = rand($quantityRange[0], $quantityRange[1]);
                $expirationDate = Carbon::now()->addMonths(rand(1, 24))->format('Y-m-d');

                // Compute status based on quantity and expiration date
                $status = $this->computeStatus($quantity, $expirationDate);



                DB::table('medicines')->insert([
                    'user_id' => $userId,
                    'batch_id' => $batchId,
                    'barangay_id' => $barangayId,
                    'generic_name_id' => $genericName->id,
                    'brand' => $brandName,
                    'quantity' => $quantity,
                    'reserved' => null,
                    'provider' => $providers[array_rand($providers)],
                    'category_id' => $genericName->category_id,
                    'expiration_date' => $expirationDate,
                    'status' => $status,
                    'date_acquired' => Carbon::now()->subDays(rand(1, 365))->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    private function computeStatus($quantity, $expirationDate)
    {
        $currentDate = Carbon::now();
        $expiryDate = Carbon::parse($expirationDate);
        $threeMonthsFromNow = $currentDate->copy()->addMonths(3);
        $statuses = [];

        // Check for Expired status
        if ($expiryDate < $currentDate) {
            return "Expired";
        }

        // Quantity-based status
        if ($quantity === 0) {
            $statuses[] = "Out of stock";
        } elseif ($quantity <= 20) {
            $statuses[] = "Low on stock";
        } else {
            $statuses[] = "On stock";
        }

        // Check for Expiring status
        if ($expiryDate <= $threeMonthsFromNow) {
            $statuses[] = "Expiring";
        }

        return implode(', ', $statuses);
    }
}
