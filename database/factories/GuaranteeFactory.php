<?php

namespace Database\Factories;

use App\Models\Guarantee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class GuaranteeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Guarantee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $guaranteeTypes = ['Bank', 'Bid Bond', 'Insurance', 'Surety'];
        $currencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF'];
        $statuses = ['draft', 'review', 'applied', 'issued', 'rejected'];
        
        return [
            'corporate_reference_number' => 'TFG-' . date('Ymd') . '-' . strtoupper($this->faker->bothify('???###')),
            'guarantee_type' => $this->faker->randomElement($guaranteeTypes),
            'nominal_amount' => $this->faker->randomFloat(2, 10000, 250000),
            'nominal_amount_currency' => $this->faker->randomElement($currencies),
            'expiry_date' => $this->faker->dateTimeBetween('+1 month', '+2 years'),
            'applicant_name' => $this->faker->company,
            'applicant_address' => $this->faker->address,
            'beneficiary_name' => $this->faker->company,
            'beneficiary_address' => $this->faker->address,
            'status' => $this->faker->randomElement($statuses),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Set the guarantee's status to draft.
     *
     * @return Factory
     */
    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
            ];
        });
    }

    /**
     * Set the guarantee's status to review.
     *
     * @return Factory
     */
    public function review()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'review',
            ];
        });
    }

    /**
     * Set the guarantee's status to applied.
     *
     * @return Factory
     */
    public function applied()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'applied',
            ];
        });
    }

    /**
     * Set the guarantee's status to issued.
     *
     * @return Factory
     */
    public function issued()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'issued',
            ];
        });
    }

    /**
     * Set the guarantee's status to rejected.
     *
     * @return Factory
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
            ];
        });
    }
}