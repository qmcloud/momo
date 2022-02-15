package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"github.com/zeromicro/go-zero/core/logx"
	coreV1 "k8s.io/api/core/v1"
	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
	"log"
	"strconv"
	"strings"
)

type CreateServiceLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewCreateServiceLogic(ctx context.Context, svcCtx *svc.ServiceContext) CreateServiceLogic {
	return CreateServiceLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *CreateServiceLogic) CreateService(req types.RequestCreateService) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	service := &coreV1.Service{
		ObjectMeta: metav1.ObjectMeta{
			Name:   req.Name,
			Labels: getLabelsMap(req.Labels),
		},
		Spec: coreV1.ServiceSpec{
			Type:     coreV1.ServiceTypeNodePort,
			Selector: getSelector(req.Labels, req.AppName),
			Ports:    getServicePorts(req.Ports),
		},
	}
	createService, err := clientSet.CoreV1().Services(req.Namespace).Create(context.TODO(), service, metav1.CreateOptions{})
	return nil, errorx.NewCodeError(200, "查询成功", createService)
}

func getServicePorts(portsStr string) []coreV1.ServicePort {
	portsList := make([]coreV1.ServicePort, 0, 5)
	ports := strings.Split(portsStr, "\n")
	for _, port := range ports {
		values := strings.SplitN(port, ":", 3)
		if len(values) != 3 {
			continue
		}
		// todo 端口范围检查
		intPort, err := strconv.Atoi(values[1])
		if err != nil {
			log.Fatal(err)
			continue
		}
		protocol := coreV1.ProtocolTCP
		if strings.Compare(strings.ToLower(values[0]), "tcp") != 0 {
			protocol = coreV1.ProtocolUDP
		}
		portsList = append(portsList, coreV1.ServicePort{
			Name:     strings.TrimSpace(values[0]),
			Protocol: protocol,
			Port:     int32(intPort),
		})
	}
	return portsList
}
