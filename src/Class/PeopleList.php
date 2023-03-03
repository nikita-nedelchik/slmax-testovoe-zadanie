<?php

namespace App\Class;

class PeopleList
{
    private array $peopleListId;
    private static \PDO $dbConnection;

    public function __construct(array $criteria, string $condition)
    {
        self::$dbConnection = (PeopleDatabase::getInstance())->getConnection();
        self::$dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        foreach ($criteria as $key => $criterion) {
            $field = $key;
            $value = $criterion;
        }

        $sql = "SELECT id FROM `people` WHERE $field $condition $value";
        $statement = self::$dbConnection->prepare($sql);
        $statement->execute();
        $arraysId = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($arraysId as $item) {
            $this->peopleListId[] = $item['id'];
        }
    }

    public function getPeopleById(): array
    {
        $where = PeopleDatabase::getWhereStatement($this->peopleListId);
        $sql = "SELECT * FROM `people` $where";

        $sth = self::$dbConnection->prepare($sql);
        $sth->execute();
        $peopleArray =  $sth->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($peopleArray as $humanArr) {
            $human = new People();
            $human->setId($humanArr['id']);
            $human->setGender($humanArr['gender']);
            $human->setFirstName($humanArr['first_name']);
            $human->setLastName($humanArr['last_name']);
            $human->setBirthPlace($humanArr['birth_place']);
            $human->setBirthDate($humanArr['birth_date']);
            $arrObjects[] = $human;
        }

        return $arrObjects;
    }

    public function deleteHuman(People $human): void
    {
        $sql = "DELETE FROM people WHERE id=:id";
        $statement = self::$dbConnection->prepare($sql);
        $id = $human->getId();
        $statement->bindParam(":id", $id);
        $statement->execute();
    }
}
