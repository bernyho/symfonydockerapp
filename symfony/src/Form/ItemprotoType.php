<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemprotoType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $builder
			->add('from')
			->add('to')
			->add('name')
			->add('type', ChoiceType::class, [
                'choices' => $this->getItemTypes()
            ])
			->add('subtype', ChoiceType::class, [
                'choices' => $this->getItemSubtypes()
            ])
			->add('size', IntegerType::class, [
                'data'=>'2'
            ])
			->add('antiflag', ChoiceType::class, [
                'choices' => $this->getAntiflag(),
                'label' => 'pro koho je?'
            ])
			->add('flag', TextType::class, ['data'=>'ITEM_TUNABLE'])
			->add('gold')
			->add('shopBuy')
			->add('refineset')
			->add('level')
			->add('levelplus', IntegerType::class, [
                'data'=>'0',
                'label' => 'Dynam. zvednout lvl'
            ])
			->add('minattack')
			->add('maxattack')
            ->add('minmagicattack',IntegerType::class, [
                'data'=>'0',
                'label' => 'Min Magic'
            ])
            ->add('maxmagicattack',IntegerType::class, [
                'data'=>'0',
                'label' => 'Max Magic'
            ])
			->add('bonusPercent')
			->add('skillAndAverage', ChoiceType::class, [
                'choices' => [
                    'ano' => 'ano',
                    'ne' => 'ne',
                ]
            ])
            ->add('applytype0', ChoiceType::class, [
                'choices' => $this->getItemApply()
            ])
            ->add('applytype1', ChoiceType::class, [
                'choices' => $this->getItemApply()
            ])
            ->add('applytype2', ChoiceType::class, [
                'choices' => $this->getItemApply()
            ])
            ->add('applyvalue0', IntegerType::class, [
                'data' => 0
            ])
            ->add('applyvalue1', IntegerType::class, [
                'data' => 0
            ])
            ->add('applyvalue2', IntegerType::class, [
                'data' => 0
            ])
            ->add('plusvalue0', IntegerType::class, [
                'data' => 0
            ])
            ->add('plusvalue1', IntegerType::class, [
                'data' => 0
            ])
            ->add('plusvalue2', IntegerType::class, [
                'data' => 0
            ])
			->add('save', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => null,
		]);
	}

    private function getItemApply(): array
    {
        return [
            "APPLY_NONE" => "APPLY_NONE",
            "APPLY_ATT_SPEED" => "APPLY_ATT_SPEED",
            "APPLY_CAST_SPEED" => "APPLY_CAST_SPEED",
            "APPLY_CRITICAL_PCT" => "APPLY_CRITICAL_PCT",
            "APPLY_PENETRATE_PCT" => "APPLY_PENETRATE_PCT",
            "APPLY_ATTBONUS_HUMAN" => "APPLY_ATTBONUS_HUMAN",
            "APPLY_ATTBONUS_ANIMAL" => "APPLY_ATTBONUS_ANIMAL",
            "APPLY_ATTBONUS_ORC" => "APPLY_ATTBONUS_ORC",
            "APPLY_ATTBONUS_DEVIL" => "APPLY_ATTBONUS_DEVIL",
        ];
    }

    private function getItemTypes(): array
    {
        return [
            "ITEM_WEAPON" => "ITEM_WEAPON",
            //"ITEM_ARMOR" => "ITEM_ARMOR",
//            "ITEM_NONE" => "ITEM_NONE",
//            "ITEM_USE" => "ITEM_USE",
//            "ITEM_AUTOUSE" => "ITEM_AUTOUSE",
//            "ITEM_MATERIAL" => "ITEM_MATERIAL",
//            "ITEM_SPECIAL" => "ITEM_SPECIAL",
//            "ITEM_TOOL" => "ITEM_TOOL",
//            "ITEM_LOTTERY" => "ITEM_LOTTERY",
//            "ITEM_ELK" => "ITEM_ELK",
//            "ITEM_METIN" => "ITEM_METIN",
//            "ITEM_CONTAINER" => "ITEM_CONTAINER",
//            "ITEM_FISH" => "ITEM_FISH",
//            "ITEM_ROD" => "ITEM_ROD",
//            "ITEM_RESOURCE" => "ITEM_RESOURCE",
//            "ITEM_CAMPFIRE" => "ITEM_CAMPFIRE",
//            "ITEM_UNIQUE" => "ITEM_UNIQUE",
//            "ITEM_SKILLBOOK" => "ITEM_SKILLBOOK",
//            "ITEM_QUEST" => "ITEM_QUEST",
//            "ITEM_POLYMORPH" => "ITEM_POLYMORPH",
//            "ITEM_TREASURE_BOX" => "ITEM_TREASURE_BOX",
//            "ITEM_TREASURE_KEY" => "ITEM_TREASURE_KEY",
//            "ITEM_SKILLFORGET" => "ITEM_SKILLFORGET",
//            "ITEM_GIFTBOX" => "ITEM_GIFTBOX",
//            "ITEM_PICK" => "ITEM_PICK",
//            "ITEM_HAIR" => "ITEM_HAIR",
//            "ITEM_TOTEM" => "ITEM_TOTEM",
//            "ITEM_BLEND" => "ITEM_BLEND",
//            "ITEM_COSTUME" => "ITEM_COSTUME",
//            "ITEM_DS" => "ITEM_DS",
//            "ITEM_SPECIAL_DS" => "ITEM_SPECIAL_DS",
//            "ITEM_EXTRACT" => "ITEM_EXTRACT",
//            "ITEM_SECONDARY_COIN" => "ITEM_SECONDARY_COIN",
//            "ITEM_RING" => "ITEM_RING",
//            "ITEM_BELT" => "ITEM_BELT"
        ];
    }

    private function getItemSubtypes(): array
    {
        return [
            "ITEM_WEAPON" => [
                "WEAPON_SWORD" => "WEAPON_SWORD",
                "WEAPON_DAGGER" => "WEAPON_DAGGER",
                "WEAPON_BOW" => "WEAPON_BOW",
                "WEAPON_TWO_HANDED" => "WEAPON_TWO_HANDED",
                "WEAPON_BELL" => "WEAPON_BELL",
                "WEAPON_FAN" => "WEAPON_FAN",
                "WEAPON_ARROW" => "WEAPON_ARROW",
                "WEAPON_MOUNT_SPEAR" => "WEAPON_MOUNT_SPEAR",
                "WEAPON_NUM_TYPES" => "WEAPON_NUM_TYPES",
            ],
            "ITEM_ARMOR" => [],
        ];
    }

    private function getAntiflag(): array
    {
        // ANTI_MUSA : Unusable by warrior
        // ANTI_ASSASSIN : Unusable by ninja
        // ANTI_SURA : Unusable by sura
        // ANTI_MUDANG : Unusable by shaman
        // ANTI_WOLFMAN : Unusable by lycan
        return [
            'war, ninja, sura' => 'ANTI_MUDANG|ANTI_WOLFMAN',
            'sura' => 'ANTI_MUSA|ANTI_ASSASSIN|ANTI_MUDANG|ANTI_WOLFMAN',
            'ninja' => 'ANTI_MUSA|ANTI_SURA|ANTI_MUDANG|ANTI_WOLFMAN',
            'warrior' => 'ANTI_ASSASSIN|ANTI_SURA|ANTI_MUDANG|ANTI_WOLFMAN',
            'shaman' => 'ANTI_MUSA|ANTI_ASSASSIN|ANTI_SURA|ANTI_WOLFMAN',
        ];
    }
}
