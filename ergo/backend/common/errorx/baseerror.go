package errorx

const defaultCode = 1001

type CodeError struct {
	Code    int         `json:"code"`
	Message string      `json:"message"`
	Data    interface{} `json:"data"`
}

type CodeErrorResponse struct {
	Code    int         `json:"code"`
	Message string      `json:"message"`
	Data    interface{} `json:"data"`
}

func NewCodeError(code int, message string, data interface{}) error {
	return &CodeError{Code: code, Message: message, Data: data}
}

func NewDefaultError(message string, data interface{}) error {
	return NewCodeError(defaultCode, message, data)
}

func (e *CodeError) Error() string {
	return e.Message
}

func (e *CodeError) DataInfo() *CodeErrorResponse {
	return &CodeErrorResponse{
		Code:    e.Code,
		Message: e.Message,
		Data:    e.Data,
	}
}
