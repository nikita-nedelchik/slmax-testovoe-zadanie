<?php

namespace App\Class;

use stdClass;

class People
{
    const MAN = 0;
    const WOMAN = 1;

    private int $id;
    private int $gender;
    private string $firstName;
    private string $lastName;
    private string $birthPlace;
    private string $birthDate;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getGender(): int
    {
        return $this->gender;
    }

    public function setGender(int $gender): void
    {
        $this->gender = $gender;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBirthPlace(): string
    {
        return $this->birthPlace;
    }

    public function setBirthPlace(string $birthPlace): void
    {
        $this->birthPlace = $birthPlace;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    public function setBirthDate(string $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    private static \PDO $dbConnection;

    public function __construct(
        int $gender = 20,
        string $firstName = '',
        string $lastName = '',
        string $birthPlace = '',
        string $birthDate = ''
    ) {
        $this->gender = $gender;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthDate = $birthDate;
        $this->birthPlace = $birthPlace;

        self::$dbConnection = (PeopleDatabase::getInstance())->getConnection();
        self::$dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        if ($firstName !== '' && $lastName !== '' && $birthPlace !== '' && $gender !== 20 && $birthDate !== '') {
            $this->createHuman($gender, $firstName, $lastName, $birthPlace, $birthDate);
        }
    }

    public function deleteHumanById(int $id): void
    {
        $sql = "DELETE FROM people WHERE id=:id";
        $statement = self::$dbConnection->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->execute();
    }

    public static function getGenderNameByNumber(int $number): string
    {
        return $number === 0 ? 'Мужчина' : ($number === 1 ? 'Женщина' : '');
    }

    public static function getHumanAge(string $birthDate): int
    {
        $diff = date('Ymd') - date('Ymd', strtotime($birthDate));
        return substr( $diff, 0, -4 );
    }

    public function updateHumanAgeOrGender(int $humanId, int $age = 0, string $gender = ''): object
    {
        $sql = "SELECT * FROM `people` WHERE id = :id";
        $statement = self::$dbConnection->prepare($sql);
        $statement->bindParam(":id", $humanId);
        $statement->execute();
        $human = $statement->fetch(\PDO::FETCH_ASSOC);
        if (empty($human)) {
            return new StdClass();
        }

        if ($gender === 'Мужчина') {
            $gender = 0;
        } elseif ($gender === 'Женщина') {
            $gender = 1;
        }

        $age = $age === 0 ? $human['birth_date'] : date('Y-m-d', strtotime(date("d-m-Y") . " - $age years"));
        $gender = $gender !== '' ? $gender : (int)$human['gender'];

        $newSql = "UPDATE `people` SET gender = :gen, birth_date = :birth WHERE id = :id";
        $statement = self::$dbConnection->prepare($newSql);
        $statement->bindParam(":id", $humanId);
        $statement->bindParam(":gen", $gender);
        $statement->bindParam(":birth", $age);
        $statement->execute();

        $sql = "SELECT * FROM `people` WHERE id = :id";
        $statement = self::$dbConnection->prepare($sql);
        $statement->bindParam(":id", $humanId);
        $statement->execute();
        return $statement->fetchObject();
    }

    private function createHuman(
        int    $gender,
        string $firstName,
        string $lastName,
        string $birthPlace,
        string $birthDate
    ):void {
        $sql = "INSERT INTO `people` (first_name, last_name, gender, birth_date, birth_place) VALUES (:first_name, :last_name, :gender,  :birth_date, :birth_place)";
        $statm = self::$dbConnection->prepare($sql);
        $statm->bindParam(":first_name", $firstName);
        $statm->bindParam(":last_name", $lastName);
        $statm->bindParam(":gender", $gender);
        $format = date('Y-m-d', strtotime($birthDate));
        $statm->bindParam(":birth_date", $format);
        $statm->bindParam(":birth_place", $birthPlace);
        $statm->execute();
    }
}
