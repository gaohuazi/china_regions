package province

import (
	"encoding/json"
	"regexp"
	"strconv"
	"strings"
	"fmt"

	"../util"
)

const PROVINCE_URL = "https://misc.360buyimg.com/jdf/1.0.0/ui/area/1.0.0/area.js"

type Item struct {
	Id   int    `json:"id"`
	Name string `json:"name"`
}

type Province struct {
	Data []Item
	Json string
}

func (this *Province) ToJson() string {
	tempJson, _ := json.Marshal(&this.Data)
	this.Json = string(tempJson)

	return this.Json
}

func (this *Province) AppendData(item Item) {
	this.Data = append(this.Data, item)
}

func (this *Province) Run() {
	html := util.DownloadUrl(PROVINCE_URL)
	reProvince := `a\.each\(\"(.*?)\"`
	re := regexp.MustCompile(reProvince)
	findStr := re.FindString(html)
	// findStr := re.FindAllStringSubmatch(html, -1)
	// fmt.Printf("%v", findStr)

	re = regexp.MustCompile(`\"(.*?)\"`)
	findStr = re.FindString(findStr)
	findStr = strings.Trim(findStr, `"`)
	tempSlice := strings.Split(findStr, ",")

	var item Item
	for _, tempItem := range tempSlice {
		provinceSlice := strings.Split(tempItem, "|")

		id, _ := strconv.Atoi(provinceSlice[1:2][0])
		name := provinceSlice[0:1][0]
		
		jsonstr := fmt.Sprintf(`{"Id":%d,"Name":"%s"}`, id, name)
		json.Unmarshal([]byte(jsonstr), &item)
		this.AppendData(item)
	}

	this.ToJson()
}
