<?php

/**
 * This is the model class for table "material".
 *
 * The followings are the available columns in table 'material':
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $author_id
 * @property string $updated
 * @property string $created
 * @property integer $status
 * @property string $type
 * @property string $tags
 *
 * The followings are the available model relations:
 * @property MaterialRelation[] $materialRelations
 * @property MaterialRelation[] $materialRelations1
 * @property MaterialTags[] $materialTags
 */
class Material extends CActiveRecord {

    private $_oldTags;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'material';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('author_id, status', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 300),
            array('type', 'length', 'max' => 20),
            array('content, updated, created, tags', 'safe'),
            array('tags', 'match', 'pattern' => '/^[\w\s,]+$/',
                'message' => 'В тегах можно использовать только буквы.'),
           // array('tags', 'normalizeTags'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, content, author_id, updated, created, status, type, tags', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'author' => array(self::BELONGS_TO, 'User', 'author_id'),
            'tags' => array(self::HAS_MANY, 'MaterialTags', 'material_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'Идентификатор',
            'title' => 'Заголовок',
            'content' => 'Контент',
            'author_id' => 'Автор',
            'updated' => 'Редактировано',
            'created' => 'Создано',
            'status' => 'Статус',
            'type' => 'тип материала',
            'tags' => 'Tags',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('author_id', $this->author_id);
        $criteria->compare('updated', $this->updated, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('tags', $this->tags, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Material the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->author_id = Yii::app()->user->id;
        }

        return parent::beforeSave();
    }

    protected function afterSave() {
        //Tag::model()->updateFrequency($this->_oldTags, $this->tags);
        return parent::afterSave();
    }

    protected function afterFind() {
        $this->_oldTags = $this->tags;
        return parent::afterFind();
    }

    protected function afterDelete() {
      
        //Tag::model()->updateFrequency($this->tags, '');
        parent::afterDelete();
    }

    public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'updated',
            )
        );
    }

    public static function getAllTypes() {
        return array(
            'software' => 'П.З. и О.С.',
            'attack' => 'Атака',
            'program' => 'Программы',
            'settings' => 'настройки',
            'article' => 'Статьи'
        );
    }



}
