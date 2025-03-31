<?php

declare(strict_types=1);

namespace Buddy\Repman\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Timezones;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Timezone;

class ChangeTimezoneType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return '';
    }

    /**
     * @param array<mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $zones = [];

        // Holen Sie sich nur gültige Zeitzonen
        foreach (Timezones::getIds() as $zone) {
            try {
                // Versuchen, den Namen und den GMT-Offset der Zeitzone zu holen
                $zoneName = Timezones::getName($zone);
                $gmtOffset = Timezones::getGmtOffset($zone);

                // Wenn die Zeitzone gültig ist, füge sie zu den Auswahlmöglichkeiten hinzu
                if ($zoneName && $gmtOffset !== false) {
                    $zones[sprintf('%s %s', $zoneName, $gmtOffset)] = $zone;
                }
            } catch (\Exception $e) {
                // Ungültige Zeitzonen überspringen
                continue;
            }
        }

        // Formular mit den validierten Zeitzonen
        $builder
            ->add('timezone', ChoiceType::class, [
                'choices' => $zones,
                'label' => false,
                'attr' => [
                    'class' => 'form-control selectpicker',
                    'data-live-search' => 'true',
                    'data-style' => 'btn-secondary',
                    'data-size' => 10,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Timezone(),
                ],
            ])
            ->add('changeTimezone', SubmitType::class, ['label' => 'Change timezone']);
    }
}
