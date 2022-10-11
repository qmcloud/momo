package response

import (
	"github.com/zeromicro/go-zero/rest/httpx"
	"net/http"
)

type CodeError struct {
	Code    int    `json:"code"`
	Message string `json:"message"`
}

func (m *CodeError) Error() string {
	return m.Message
}

type SuccessData struct {
	Code    int         `json:"code"`
	Data    interface{} `json:"data"`
	Message string      `json:"message"`
}

func Response(w http.ResponseWriter, r *http.Request, resp interface{}, err error) {
	if err == nil {
		httpx.WriteJson(w, http.StatusOK, Success(resp))
	} else {
		if e, ok := err.(*CodeError); ok {
			httpx.WriteJson(w, http.StatusOK, e)
		} else {
			httpx.WriteJson(w, http.StatusBadRequest, struct {
				Message string `json:"message"`
			}{Message: err.Error()})
		}
	}
}

func Success(data interface{}) *SuccessData {
	return &SuccessData{
		Code:    200,
		Data:    data,
		Message: "",
	}
}

func Error(code int, message string) *CodeError {
	return &CodeError{
		Code:    code,
		Message: message,
	}
}
