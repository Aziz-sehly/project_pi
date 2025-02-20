<?php

namespace App\Form;

use App\Entity\Region;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;  // Add this for file upload
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Choice;

class RegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('pays', ChoiceType::class, [
                'choices' => array_combine($this->getCountries(), $this->getCountries()), // Labels = Values
                'placeholder' => 'Sélectionnez un pays',
                'constraints' => [
                    new NotBlank(['message' => 'Le pays est obligatoire']),
                    new Choice([
                        'choices' => $this->getCountries(),
                        'message' => 'Choisissez un pays valide',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-select', // Bootstrap style
                ],
            ])
            ->add('adresse')
            ->add('description')
            ->add('image', FileType::class, [  // Add this to handle image upload
                'label' => 'Image (JPEG/PNG file)', 
                'mapped' => false, // The image field is not mapped to the entity directly
                'required' => false,  // Make it optional
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',  // Optional: Limit file size to 1MB
                        'mimeTypes' => ['image/jpeg', 'image/png'],  // Accept only JPEG/PNG images
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                    ])
                ],
            ])
        ;

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Region::class,
        ]);
    }

    private function getCountries(): array
    {
        return [
            "Afghanistan", "Albanie", "Algérie", "Andorre", "Angola", "Antigua-et-Barbuda", "Argentine", "Arménie", "Australie", "Autriche",
            "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh", "Barbade", "Belgique", "Belize", "Bénin", "Bhoutan", "Biélorussie", "Bolivie",
            "Bosnie-Herzégovine", "Botswana", "Brésil", "Brunei", "Bulgarie", "Burkina Faso", "Burundi", "Cambodge", "Cameroun", "Canada",
            "Cap-Vert", "République centrafricaine", "Tchad", "Chili", "Chine", "Colombie", "Comores", "Congo", "République démocratique du Congo",
            "Costa Rica", "Croatie", "Cuba", "Chypre", "République tchèque", "Danemark", "Djibouti", "Dominique", "République dominicaine", "Équateur",
            "Égypte", "Salvador", "Guinée équatoriale", "Érythrée", "Estonie", "Eswatini", "Éthiopie", "Fidji", "Finlande", "France", "Gabon",
            "Gambie", "Géorgie", "Allemagne", "Ghana", "Grèce", "Grenade", "Guatemala", "Guinée", "Guinée-Bissau", "Guyana", "Haïti", "Honduras",
            "Hongrie", "Islande", "Inde", "Indonésie", "Iran", "Irak", "Irlande", "Israël", "Italie", "Côte d'Ivoire", "Jamaïque", "Japon",
            "Jordanie", "Kazakhstan", "Kenya", "Kiribati", "Koweït", "Kirghizistan", "Laos", "Lettonie", "Liban", "Lesotho", "Libéria", "Libye",
            "Liechtenstein", "Lituanie", "Luxembourg", "Madagascar", "Malawi", "Malaisie", "Maldives", "Mali", "Malte", "Îles Marshall", "Mauritanie",
            "Maurice", "Mexique", "Micronésie", "Moldavie", "Monaco", "Mongolie", "Monténégro", "Maroc", "Mozambique", "Myanmar", "Namibie",
            "Nauru", "Népal", "Pays-Bas", "Nouvelle-Zélande", "Nicaragua", "Niger", "Nigeria", "Corée du Nord", "Macédoine du Nord", "Norvège",
            "Oman", "Pakistan", "Palaos", "Palestine", "Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pérou", "Philippines", "Pologne",
            "Portugal", "Qatar", "Roumanie", "Russie", "Rwanda", "Saint-Christophe-et-Niévès", "Sainte-Lucie", "Saint-Vincent-et-les-Grenadines",
            "Samoa", "Saint-Marin", "Sao Tomé-et-Principe", "Arabie saoudite", "Sénégal", "Serbie", "Seychelles", "Sierra Leone", "Singapour",
            "Slovaquie", "Slovénie", "Îles Salomon", "Somalie", "Afrique du Sud", "Corée du Sud", "Soudan du Sud", "Espagne", "Sri Lanka",
            "Soudan", "Suriname", "Suède", "Suisse", "Syrie", "Tadjikistan", "Tanzanie", "Thaïlande", "Timor oriental", "Togo", "Tonga",
            "Trinité-et-Tobago", "Tunisie", "Turquie", "Turkménistan", "Tuvalu", "Ouganda", "Ukraine", "Émirats arabes unis", "Royaume-Uni",
            "États-Unis", "Uruguay", "Ouzbékistan", "Vanuatu", "Vatican", "Venezuela", "Vietnam", "Yémen", "Zambie", "Zimbabwe"
        ];
    }
}
