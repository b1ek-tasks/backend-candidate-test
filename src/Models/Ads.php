<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Shuchkin\SimpleXLSX;

class Ads extends Model
{
    protected $table = 'ads';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'date',
        'expenditures',
        'title',
        'campaign',
        'set',
        'views',
        'clicks'
    ];

    public static function fromXlsx(SimpleXLSX $file)
    {
        $rows = $file->rows();

        /**
         * @var Collection<int, Ads>
         */
        $ads = Ads::all();

        $campaigns = AdsCampaign::all();
        $sets = AdsAdset::all();

        $newCampaigns = false;
        $newSets = false;

        /*
        Array
        (
            [0] => Дата
            [1] => Расходы
            [2] => ID объявления
            [3] => Название объявления
            [4] => ID кампании
            [5] => Название кампании
            [6] => ID группы
            [7] => Название группы
            [8] => Показы
            [9] => Клики
        )
        */

        foreach ($rows as $index => $row) {
            if ($index == 0) {
                continue;
            }
            if ($ads->contains((int) $row[2])) {
                continue;
            }

            $ads->push(new Ads([
                'id' => (int) $row[2],
                'date' => $row[0],
                'expenditures' => $row[1],
                'title' => $row[3],
                'campaign' => $row[4],
                'set' => $row[6],
                'views' => $row[8],
                'clicks' => $row[9]
            ]));

            if (!$campaigns->contains($row[4])) {
                $campaigns[] = new AdsCampaign([
                    'id' => $row[4],
                    'title' => $row[5]
                ]);
                $newCampaigns = true;
            }

            if (!$sets->contains($row[6])) {
                $sets[] = new AdsAdset([
                    'id' => $row[6],
                    'title' => $row[7]
                ]);
                $newSets = true;
            }
        }

        if ($newCampaigns) {
            AdsCampaign::upsert($campaigns->toArray(), [ 'id' ]);
        }
        if ($newSets) {
            AdsAdset::upsert($sets->toArray(), [ 'id' ]);
        }

        Ads::upsert($ads->toArray(), [ 'id' ]);
    }
}
